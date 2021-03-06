#!make
include .env

OS=$(shell uname)

ifeq ($(OS),Darwin)
    export UID = 1000
    export GID = 1000
else
    export UID = $(shell id -u)
    export GID = $(shell id -g)
endif

.DEFAULT_GOAL := help
RUN_IN_CONTAINER := docker exec -it ${PROJECT_NAME}
SUBCOMMAND = $(subst +,-, $(filter-out $@,$(MAKECMDGOALS)))

.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | cut -d: -f2- | sort -t: -k 2,2 | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

include ./makefiles/*.mk
include server/Makefile
