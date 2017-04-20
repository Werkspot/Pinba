<?php
declare(strict_types=1);

namespace Werkspot\Pinba\Elastica;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * We can't test the
 */
class TimedPaginatedFinderTest extends TestCase
{
    public function testFind()
    {
        $parentMock = Mockery::mock(PaginatedFinderInterface::class);
        $parentMock->shouldReceive('find')->with('foo', 10, ['some_opt'])->andReturn('the result');

        $finder = new TimedPaginatedFinder($parentMock, 'some_meta');
        $this->assertSame('the result', $finder->find('foo', 10, ['some_opt']));

    }

    public function testFind_Tags()
    {
        // Separate test from testFind() so we can still test the implementation of find() even if we don't have pinba,
        // otherwise markTestAsSkipped would not test that
        $this->testFind();
        $this->assertCorrectTagsSet([
            'group' => 'elasticsearch',
            'op' => 'find',
            'meta' => 'some_meta'
        ]);
    }

    public function testFindPaginated()
    {
        $parentMock = Mockery::mock(PaginatedFinderInterface::class);
        $parentMock->shouldReceive('findPaginated')->with('foo', ['some_opt'])->andReturn('the result');

        $finder = new TimedPaginatedFinder($parentMock, 'some_meta');
        $this->assertSame('the result', $finder->findPaginated('foo', ['some_opt']));
    }

    public function testFindPaginated_Tags()
    {
        // Separate test from so we can still test the implementation of find() even if we don't have pinba,
        // otherwise markTestAsSkipped would not test that
        $this->testFindPaginated();
        $this->assertCorrectTagsSet([
            'group' => 'elasticsearch',
            'op' => 'findPaginatedd', // THIS SHOULD STILL FAIL
            'meta' => 'some_meta'
        ]);
    }

    public function testCreatePaginatorAdapter()
    {
        $parentMock = Mockery::mock(PaginatedFinderInterface::class);
        $parentMock->shouldReceive('createPaginatorAdapter')->with('foo', ['some_opt'])->andReturn('the result');

        $finder = new TimedPaginatedFinder($parentMock, 'some_meta');
        $this->assertSame('the result', $finder->createPaginatorAdapter('foo', ['some_opt']));
    }

    public function testCreatePaginatorAdapter_Tags()
    {
        // Separate test from so we can still test the implementation of find() even if we don't have pinba,
        // otherwise markTestAsSkipped would not test that
        $this->testCreatePaginatorAdapter();

        $this->assertCorrectTagsSet(
            [
                'group' => 'elasticsearch',
                'op' => 'createPaginatorAdapter',
                'meta' => 'some_meta'
            ]
        );
    }

    public function testCreateRawPaginatorAdapter()
    {
        $parentMock = Mockery::mock(PaginatedFinderInterface::class);
        $parentMock->shouldReceive('createRawPaginatorAdapter')->with('foo', ['some_opt'])->andReturn('the result');

        $finder = new TimedPaginatedFinder($parentMock, 'some_meta');
        $this->assertSame('the result', $finder->createRawPaginatorAdapter('foo', ['some_opt']));
    }

    public function testCreateRawPaginatorAdapter_Tags()
    {
        // Separate test from so we can still test the implementation of find() even if we don't have pinba,
        // otherwise markTestAsSkipped would not test that
        $this->testCreatePaginatorAdapter();

        $this->assertCorrectTagsSet(
            [
                'group' => 'elasticsearch',
                'op' => 'createRawPaginatorAdapter',
                'meta' => 'some_meta'
            ]
        );
    }

    private function assertCorrectTagsSet($tags)
    {
        if (function_exists('pinba_tags_get')) {
            $this->assertSame($tags, pinba_tags_get());
        } else {
            $this->markTestSkipped('The pinba extension is not installed');
        }
    }
}
