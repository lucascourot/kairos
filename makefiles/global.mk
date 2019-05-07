#!make
.PHONY: start stop recreate restart docker-images docker-build docker-tag docker-login docker-push test check

# All

start: start-server

stop: stop-server

restart: stop-server start-server

recreate: recreate-server

# Prod
docker-images: docker-images-server

docker-build: docker-build-server

docker-tag: docker-tag-server

docker-login:
	@docker login -u ${DOCKER_USER} -p ${DOCKER_PASSWORD}

docker-push: docker-push-server

test: test-server

check: check-server
