CONFIG_FILES := \
	config/components/web/request--cookie.php \
	config/params/git-revision.php

JS_SRC_FILES := $(shell find web/js \( -type f \( -name '*.min.js' -prune \) -or \( -name '*.js' -print \) \))
JS_DEST_FILES := $(patsubst %.js,%.min.js,$(JS_SRC_FILES))

.PHONY: all
all: init $(JS_DEST_FILES)

.PHONY: init
init: node_modules vendor $(CONFIG_FILES) web/favicon.ico

.PHONY: clean
clean:
	rm -rf \
		$(JS_DEST_FILES) \
		composer.phar \
		coverage.serialized \
		node_modules \
		vendor \
		web/assets/* \
		web/favicon.ico

.PHONY: test
test: vendor
	# ./tests/bin/yii migrate/up --compact=1 --interactive=0
	/usr/bin/env XDEBUG_MODE=coverage vendor/bin/codecept run unit --coverage --coverage-html=./web/coverage

.PHONY: check-style
check-style: check-style-php check-style-js

.PHONY: check-style-php
check-style-php: check-style-phpcs check-style-phpstan

.PHONY: check-style-phpcs
check-style-phpcs: vendor
	vendor/bin/phpcs

.PHONY: check-style-phpstan
check-style-phpstan: vendor
	vendor/bin/phpstan --memory-limit=1G

.PHONY: check-style-js
check-style-js: node_modules
	npx semistandard | npx snazzy

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

%.min.js: %.js node_modules
	npx babel $< | npx terser --compress --mangle -o $@

web/favicon.ico:
	curl -o $@ -fsSL https://fetus.jp/favicon.ico
