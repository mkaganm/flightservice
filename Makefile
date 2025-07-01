# Makefile for PHP Flight Service Project

.PHONY: help install test lint start clean

help:
	@echo "Available targets:"
	@echo "  install   Install PHP dependencies via Composer"
	@echo "  test      Run all tests (if tests exist)"
	@echo "  lint      Run PHP_CodeSniffer for PSR-12 linting (if installed)"
	@echo "  start     Start the PHP built-in server on localhost:8080"
	@echo "  clean     Remove vendor directory"

install:
	composer install

test:
	@if [ -d tests ]; then \
		vendor/bin/phpunit; \
	else \
		echo "No tests directory found."; \
	fi

lint:
	@if [ -f vendor/bin/phpcs ]; then \
		vendor/bin/phpcs --standard=PSR12 src/; \
	else \
		echo "phpcs not installed. Run 'composer require --dev squizlabs/php_codesniffer'"; \
	fi

start:
	php -S localhost:8080 -t public

clean:
	rm -rf vendor/

