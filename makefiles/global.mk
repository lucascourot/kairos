#!make
.PHONY: start stop recreate restart docker-images docker-build docker-tag docker-login docker-push test check

# All

start: start-server ## start all services

stop: stop-server ## stop all services

restart: stop-server start-server ## restart all services

recreate: recreate-server ## recreate all services

# Prod
docker-images: docker-images-server

docker-build: docker-build-server

docker-tag: docker-tag-server

docker-login:
	@docker login -u ${DOCKER_USER} -p ${DOCKER_PASSWORD}

docker-push: docker-push-server

test: test-server ## run all test

check: check-server ## run all check

install: start composer database ## install project with all dependencies
