#!make
.PHONY: start-deps-server stop-deps-server start-docker-server stop-docker-server ps-server start-server stop-server recreate-server docker-images-server docker-build-server docker-tag-server docker-push-server

ifeq ($(APP_ENV), test)
    DOCKER_COMPOSE_FILE?=./server/docker/dev/docker-compose.yml
else
    DOCKER_COMPOSE_FILE?=./server/docker/$(APP_ENV)/docker-compose.yml
endif

DOCKER_COMPOSE_FILE?=./server/docker/$(APP_ENV)/docker-compose.yml
DOCKER_FILE?=./server/docker/$(APP_ENV)/dockerfiles/Dockerfile
DOCKER_COMPOSE=docker-compose --file ${DOCKER_COMPOSE_FILE} --project-name=${PROJECT_NAME}
APP_IMAGE_NAME?=${PROJECT_NAME}
APP_IMAGE_TAG?=latest
APP_IMAGE_NAMESPACE?=wakeonweb
APP_IMAGE=${APP_IMAGE_NAMESPACE}/${APP_IMAGE_NAME}
COMPOSER_AUTH?=$(shell test -f ~/.composer/auth.json && cat ~/.composer/auth.json)
DOCKER_BUILD_DIR?=.

# Dependencies
start-deps-server: ## (Docker) Start dependencies (for this project only)
	@if [ -z ${DOCKER_DEPENDENCIES} ]; then \
		echo 'No dependencies in .env file !!'; \
		exit 1; \
	fi
	@wow-docker-env up ${DOCKER_DEPENDENCIES}

stop-deps-server: ## (Docker) Stop dependencies (for this project only)
	@wow-docker-env stop ${DOCKER_DEPENDENCIES}

# Project containers
start-docker-server: ## (Docker) Start containers (for this project only)
	@${DOCKER_COMPOSE} up -d

stop-docker-server: ## (Docker) Stop containers (for this project only)
	@${DOCKER_COMPOSE} down --remove-orphans

ps-server: ## (Docker) Show containers (for this project only)
	@echo "\nProject :\n"
	@${DOCKER_COMPOSE} ps
	@echo "\n\n\nOthers :\n"
	@docker ps -s | grep 'wowdockerenv'

# All

start-server: start-deps-server start-docker-server ## (Docker) Start : dependencies, docker-sync and containers (for this project only)

stop-server: stop-docker-server ## (Docker) Stop : dependencies, docker-sync and containers (for this project only)

restart-server: stop-server start-server ## (Docker) Restart containers (and docker-sync + deps) (for this project only)

recreate-server: stop-server start-deps-server ## (Docker) Restart : dependencies, docker-sync and containers with re-build app container (for this project only)
	@if [ "$(APP_ENV)" = "dev" ] || [ "$(APP_ENV)" = "test" ]; then \
		${DOCKER_COMPOSE} up --force-recreate --build -d; \
	else \
		${DOCKER_COMPOSE} up --force-recreate -d; \
	fi

# Prod
docker-images-server: ## (Docker) List wakeonweb images for prod
	@clear
	@docker images | grep "wakeonweb/${PROJECT_NAME}"

docker-build-server: ## (Docker) Build image
	@docker build \
	    --build-arg COMPOSER_AUTH='${COMPOSER_AUTH}' \
	    --file ${DOCKER_FILE} \
	    --tag ${APP_IMAGE}:${APP_IMAGE_TAG} \
	    ${DOCKER_BUILD_DIR}

docker-tag-server: ## (Docker) Tag image
	@docker tag ${APP_IMAGE}:${APP_IMAGE_TAG}

docker-push-server: ## Docker push
	@docker push ${APP_IMAGE}:${APP_IMAGE_TAG}

