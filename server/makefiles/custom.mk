#!make
# Divers

.PHONY: generate-jwt
generate-jwt: ## (OpenSSL) Generate certs for JWT
	@${RUN_IN_CONTAINER} sh -c "mkdir -p ./var/jwt"
	@${RUN_IN_CONTAINER} sh -c "openssl genrsa -aes256 -passout pass:${PROJECT_NAME} -out ./var/jwt/private.pem 4096"
	@${RUN_IN_CONTAINER} sh -c "openssl rsa -in ./var/jwt/private.pem -passin pass:${PROJECT_NAME} -pubout > ./var/jwt/public.pem"
	@${RUN_IN_CONTAINER} sh -c "openssl rsa -in ./var/jwt/private.pem -passin pass:${PROJECT_NAME}"
	@${RUN_IN_CONTAINER} sh -c "ls -lah ./var/jwt/"
