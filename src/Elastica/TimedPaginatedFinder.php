<?php
namespace Werkspot\Pinba\Elastica;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use Werkspot\Pinba\PinbaTimer;

/**
 * Decorator for PaginatedFinderInterface that adds pinba timing
 */
class TimedPaginatedFinder implements PaginatedFinderInterface
{
    /**
     * @var PaginatedFinderInterface
     */
    private $finder;

    /**
     * @var string The meta for the pinba timers
     */
    private $meta;

    /**
     * @param PaginatedFinderInterface $finder
     * @param string|null $meta
     */
    public function __construct(PaginatedFinderInterface $finder, $meta = null)
    {
        $this->finder = $finder;
        $this->meta = $meta;
    }

    /**
     * {@inheritDoc}
     */
    public function find($query, $limit = null, $options = [])
    {
        $timer = $this->startTimer('find');

        $result = $this->finder->find($query, $limit, $options);

        $timer->stop();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findPaginated($query, $options = [])
    {
        $timer = $this->startTimer('findPaginated');

        $result = $this->finder->findPaginated($query, $options);

        $timer->stop();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function createPaginatorAdapter($query, $options = [])
    {
        $timer = $this->startTimer('createPaginatorAdapter');

        $result = $this->finder->createPaginatorAdapter($query, $options);

        $timer->stop();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function createHybridPaginatorAdapter($query)
    {
        $timer = $this->startTimer('createHybridPaginatorAdapter');

        $result = $this->finder->createHybridPaginatorAdapter($query);

        $timer->stop();

        return $result;
    }

    /**
     * Creates a raw paginator adapter for this query.
     *
     * @param mixed $query
     * @param array $options
     *
     * @return PaginatorAdapterInterface
     */
    public function createRawPaginatorAdapter($query, $options = [])
    {
        $timer = $this->startTimer('createRawPaginatorAdapter');

        $result = $this->finder->createRawPaginatorAdapter($query, $options);

        $timer->stop();

        return $result;
    }

    /**
     * @param string $op The operation to time
     * @return PinbaTimer
     */
    private function startTimer($op)
    {
        $tags= [
            'group' => 'elasticsearch',
            'op' => $op
        ];

        if ($this->meta) {
            $tags['meta'] = $this->meta;
        }

        return PinbaTimer::start($tags);
    }
}
