CURRENT_BRANCH="$(shell git rev-parse --abbrev-ref HEAD)"

default: help

help:
	@echo "Usage:"
	@echo "     make [command]"
	@echo "Available commands:"
	@grep '^[^#[:space:]].*:' Makefile | grep -v '^default' | grep -v '^_' | sed 's/://' | xargs -n 1 echo ' -'

coverage:
	php -dzend_extension=xdebug.so vendor/bin/phpunit --coverage-text --coverage-clover=coverage.clover.xml

cs-fix:
	vendor/bin/php-cs-fixer fix --verbose

test:
	vendor/bin/phpunit
