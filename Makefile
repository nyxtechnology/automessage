-include env_make

.PHONY: test

drupal ?= 8
php ?= 7.2

default: run

run:
	docker-compose up -d

run-prod:
	docker-compose up -d

in:
	docker-compose exec php /bin/bash

in-nginx:
	docker-compose exec nginx /bin/bash

in-with-root:
	docker-compose exec --user root php /bin/bash

stop:
	docker-compose stop

clean:
	docker-compose down

build:
	docker-compose build