.DEFAULT_GOAL := help
.PHONY: help

help: ## show this help
	@printf "%-20s %s\n" "Target" "Description"
	@printf "%-20s %s\n" "------" "-----------"
	@sed -rn 's/^([a-zA-Z_-]+):.*?## (.*)$$/"\1" "\2"/p' < $(MAKEFILE_LIST) | sort | xargs printf "%-20s %s\n"

php-cs-fix: ## run php-cs-fixer for fixing styling issues
	vendor/bin/php-cs-fixer fix

phpstan: ## run phpstan
	vendor/bin/phpstan analyze -c phpstan.neon

phpunit: ## run phpunit with coverage
	vendor/bin/phpunit -c phpunit.xml.dist

.PHONY: deptrac
deptrac: ## run deptrac
	vendor/bin/deptrac analyze --config-file deptrac/deptrac.yaml --formatter=table
deptrac-image: ## run deptrac image creation
	vendor/bin/deptrac analyze --config-file deptrac/deptrac.yaml --formatter=graphviz-image --output=deptrac/structure.svg

encore: ## run encore for dev environment
	rm -rf public/build/*
	yarn encore dev

eslint: ## linting javascript
	yarn eslint --fix assets/js/