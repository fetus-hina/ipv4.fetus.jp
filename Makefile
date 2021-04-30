CONFIG_FILES := \
	config/components/web/request--cookie.php \
	config/params/git-revision.php

.PHONY: all
all: init

.PHONY: init
init: node_modules vendor $(CONFIG_FILES)

.PHONY: clean
clean:
	rm -rf \
		composer.phar \
		coverage.serialized \
		node_modules \
		vendor

.PHONY: test
test: vendor
	# ./tests/bin/yii migrate/up --compact=1 --interactive=0
	/usr/bin/env XDEBUG_MODE=coverage vendor/bin/codecept run unit --coverage --coverage-html=./web/coverage

.PHONY: check-style
check-style: check-style-php

.PHONY: check-style-php
check-style-php: check-style-phpcs check-style-phpstan

.PHONY: check-style-phpcs
check-style-phpcs: vendor
	vendor/bin/phpcs

.PHONY: check-style-phpstan
check-style-phpstan: vendor
	vendor/bin/phpstan --memory-limit=1G

node_modules: package-lock.json
	npm clean-install
	@touch $@

vendor: composer.lock composer.phar
	./composer.phar install --prefer-dist
	@touch $@

composer.phar:
	curl -fsSL https://getcomposer.org/installer | php -- --stable --filename=$@

config/components/web/request--cookie.php:
	bin/generate-secret > $@

.PHONY: config/params/git-revision.php
config/params/git-revision.php:
	bin/git-revison > $@
