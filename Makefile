# Freelo PHP SDK Makefile
# Use: make <target>

OPENAPI_URL ?= https://api.freelo.io/docs/v1/freelo-api.yaml
OPENAPI_FILE := .openapi/freelo-api.yaml

.PHONY: help install test analyse check clean dist-check spec generate build

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

install: ## Install dependencies
	composer install

spec: ## Download latest OpenAPI spec from $(OPENAPI_URL)
	@mkdir -p .openapi
	curl -fsSL -o $(OPENAPI_FILE) $(OPENAPI_URL)
	@echo "Downloaded $(OPENAPI_FILE) ($$(wc -c < $(OPENAPI_FILE)) bytes)"

generate: ## Regenerate models and endpoints digest from local OpenAPI spec
	composer generate

build: spec generate analyse test ## Full build: fetch spec, regenerate models, analyse, test

test: ## Run tests
	composer test

analyse: ## Run static analysis (PHPStan + PHPCS)
	composer analyse

check: ## Run all checks (tests + analysis)
	composer check

clean: ## Clean generated files
	rm -rf vendor/ coverage/ .phpunit.cache/ .phpstan/
	rm -f composer.lock coverage.xml

dist-check: ## Show what will be distributed via Composer
	@echo "=== Distribution Package Contents ==="
	@echo ""
	@echo "Files included in distribution:"
	@find src -type f -name "*.php" | wc -l | xargs -I {} echo "  - {} PHP source files"
	@echo "  - composer.json"
	@echo "  - LICENSE"
	@echo "  - README.md"
	@echo ""
	@echo "Source code size:"
	@du -sh src/
	@echo ""
	@echo "Files EXCLUDED from distribution (via .gitattributes):"
	@echo "  - tests/, docs/, examples/ (development only)"
	@echo "  - .github/, .openapi/ (CI/config)"
	@echo "  - phpunit.xml, phpstan.neon, phpcs.xml (dev tools)"
	@echo "  - CHANGELOG.md, CONTRIBUTING.md, etc."
