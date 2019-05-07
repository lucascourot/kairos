#!make
ENV_FILE_EXISTS=$(shell [ -f .env ] && echo 1 || echo 0)
ifneq ($(CI), true)
ifeq ($(ENV_FILE_EXISTS), 0)
    $(info Creating default .env file from .env.dist)
    $(shell cp .env.dist .env)
endif
include .env
endif

OS=$(shell uname)

ifeq ($(OS),Darwin)
    export UID = 1000
    export GID = 1000
else
    export UID = $(shell id -u)
    export GID = $(shell id -g)
endif

.DEFAULT_GOAL := help
DOCKER_ENV    := $(shell test -f /.dockerenv && echo -n true)
RUN_IN_CONTAINER := docker exec -it ${PROJECT_NAME}
SUBCOMMAND = $(subst +,-, $(filter-out $@,$(MAKECMDGOALS)))

include ./makefiles/*.mk
