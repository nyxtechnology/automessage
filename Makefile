include docker.mk

## app-update:			Update the xingu plataform
.PHONY: app-update
app-update:
	docker-compose up php-install
	docker-compose up php-update