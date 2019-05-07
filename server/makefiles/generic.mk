#!make
# Generic

API_BLUEPRINT := $(shell find . -name "*.apib"|head -n 1)

ifeq ($(API_BLUEPRINT),)
	API_DESCRIPTION := swagger.yml
else
	API_DESCRIPTION := $(shell basename $(API_BLUEPRINT))
endif

.PHONY: alias
alias: ## (Generic) Show some useful aliases to create
	@echo Run these commands in your shell:
	@echo 'alias dk_behat="$(RUN_IN_CONTAINER) ./vendor/bin/behat"'
	@echo 'alias dk_composer="$(RUN_IN_CONTAINER) composer"'
	@echo 'alias dk_console="$(RUN_IN_CONTAINER) ./bin/console"'
	@echo 'alias dk_php="$(RUN_IN_CONTAINER) php"'
	@echo 'alias dk_php-cs-fixer="$(RUN_IN_CONTAINER) ./vendor/bin/php-cs-fixer"'
	@echo 'alias dk_phpstan="$(RUN_IN_CONTAINER) ./vendor/bin/phpstan"'
	@echo 'alias dk_phpunit="$(RUN_IN_CONTAINER) ./bin/phpunit"'
	@echo

.PHONY: database
database:
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	./bin/console d:d:d --force && ./bin/console d:d:c && ./bin/console d:m:m --no-interaction
endif

.PHONY: behat
behat: ## (PHP) Behavior tests
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	php -d memory_limit=-1 ./vendor/bin/behat --format=progress
endif

.PHONY: check-server
check-server: cs stan ## (PHP) Launch all lint tools. A good choice for pre-commit hook

.PHONY: cs
cs: ## (PHP) Code style checker
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	./vendor/bin/php-cs-fixer fix -v --dry-run --using-cache=no
endif

.PHONY: fix
fix: ## (PHP) Code style fixer
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	./vendor/bin/php-cs-fixer fix -v
endif

.PHONY: shell
shell: ## (Docker) Enter in app container
	$(RUN_IN_CONTAINER) /bin/bash

.PHONY: stan
stan: ## (PHP) Static analysis
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	php -d memory_limit=-1 ./vendor/bin/phpstan analyse -l 7 public src translations
endif

.PHONY: test-server
test-server: unit behat dredd ## (PHP) Launch all test tools

.PHONY: unit
unit: ## (PHP) Unit tests
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	./vendor/bin/phpunit
endif

composer:
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	composer install
endif
