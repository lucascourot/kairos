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
behat: composer ## (PHP) Behavior tests
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	php -d memory_limit=-1 ./vendor/bin/behat --format=progress
endif

.PHONY: check
check: cs stan ## (PHP) Launch all lint tools. A good choice for pre-commit hook

.PHONY: cs
cs: composer ## (PHP) Code style checker
	@echo
ifneq ($(DOCKER_ENV),true)
ifeq ($(FORCE_LOCAL),true)
	./vendor/bin/php-cs-fixer fix -v --dry-run --using-cache=no
else
	$(RUN_IN_CONTAINER) $(MAKE) $@
endif
else
	./vendor/bin/php-cs-fixer fix -v --dry-run --using-cache=no
endif

.PHONY: dredd
dredd: ## (PHP) Check API implementation
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	dredd $(API_DESCRIPTION) http://127.0.0.1:8000 -r apiary -j apiaryApiKey:$(APIARY_TEST_KEY) -j apiaryApiName:$(APIARY_API_NAME) -g './bin/console server:run --env=$(APP_ENV)'
endif

.PHONY: fix
fix: composer ## (PHP) Code style fixer
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	./vendor/bin/php-cs-fixer fix -v
endif

.PHONY: security
security: composer ## (PHP) Check if application uses dependencies with known security vulnerabilities
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	./bin/console security:check
endif

.PHONY: shell
shell: ## (Docker) Enter in app container
	$(RUN_IN_CONTAINER) /bin/bash

.PHONY: stan
stan: composer ## (PHP) Static analysis
	@echo
ifneq ($(DOCKER_ENV),true)
	$(RUN_IN_CONTAINER) $(MAKE) $@
else
	php -d memory_limit=-1 ./vendor/bin/phpstan analyse -l 7 public src translations
endif

.PHONY: test
test: unit behat dredd ## (PHP) Launch all test tools

.PHONY: unit
unit: composer ## (PHP) Unit tests
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
