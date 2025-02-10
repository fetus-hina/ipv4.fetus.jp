CONFIG_FILES := \
	config/components/web/request--cookie.php \
	config/params/git-revision.php

JS_SRC_FILES := $(shell find resources/js -type f -name '*.es')
JS_DEST_FILES := $(patsubst %.es,%.min.js,$(JS_SRC_FILES))

CSS_SRC_FILES := $(shell find resources/css -type f -name '*.scss')
CSS_DEST_FILES := $(patsubst %.scss,%.min.css,$(CSS_SRC_FILES))

MESSAGE_SRC_FILES := $(shell find messages -type f -name '*.po')
MESSAGE_DEST_FILES := $(MESSAGE_SRC_FILES:.po=.mo)

YII2_JS_SRC_BASENAMES := yii.activeForm.js yii.captcha.js yii.gridView.js yii.js yii.validation.js
YII2_JS_SRC_FILES := $(addprefix vendor/yiisoft/yii2/assets/,$(YII2_JS_SRC_BASENAMES))
YII2_JS_DEST_FILES := $(patsubst %.js,%.min.js,$(YII2_JS_SRC_FILES))

EXT_JS_SRC_FILES := node_modules/patternomaly/dist/patternomaly.js
EXT_JS_DEST_FILES := $(patsubst %.js,%.min.js,$(EXT_JS_SRC_FILES))

GZIP_DEST_FILES := \
	$(patsubst %.css,%.css.gz,$(CSS_DEST_FILES)) \
	$(patsubst %.js,%.js.gz,$(EXT_JS_DEST_FILES)) \
	$(patsubst %.js,%.js.gz,$(JS_DEST_FILES)) \
	$(patsubst %.js,%.js.gz,$(YII2_JS_DEST_FILES)) \
	$(patsubst %.js,%.js.gz,$(YII2_JS_SRC_FILES))

.PHONY: all
all: \
	init \
	$(JS_DEST_FILES) \
	$(CSS_DEST_FILES) \
	$(MESSAGE_DEST_FILES) \
	$(YII2_JS_DEST_FILES) \
	$(EXT_JS_DEST_FILES) \
	$(GZIP_DEST_FILES) \
	messages

.PHONY: init
init: node_modules vendor $(CONFIG_FILES) web/favicon web/favicon.ico

.PHONY: clean
clean:
	rm -rf \
		$(CSS_DEST_FILES) \
		$(CSS_DEST_FILES:%.min.css=%.css) \
		$(EXT_JS_DEST_FILES) \
		$(GZIP_DEST_FILES) \
		$(JS_DEST_FILES) \
		$(JS_DEST_FILES:%.min.js=%.js) \
		$(MESSAGE_DEST_FILES) \
		$(YII2_JS_DEST_FILES) \
		composer.phar \
		coverage.serialized \
		node_modules \
		resources/css/_bootstrap-icons.* \
		vendor \
		web/assets/* \
		web/favicon \
		web/favicon.ico

.PHONY: test
test: vendor
	/usr/bin/env XDEBUG_MODE=coverage vendor/bin/codecept run unit

.PHONY: check-style
check-style: check-style-php check-style-js check-style-css

.PHONY: check-style-php
check-style-php: check-style-composer check-style-phpcs check-style-phpstan

.PHONY: check-style-composer
check-style-composer: vendor
	./composer.phar normalize --dry-run

.PHONY: check-style-phpcs
check-style-phpcs: vendor
	vendor/bin/phpcs

.PHONY: check-style-phpstan
check-style-phpstan: vendor
	vendor/bin/phpstan --memory-limit=1G

.PHONY: check-style-js
check-style-js: node_modules
	npx semistandard 'resources/**/*.es' | npx snazzy

.PHONY: check-style-css
check-style-css: node_modules
	npx stylelint 'resources/**/*.scss'

node_modules: package-lock.json
	npm clean-install
	@touch $@

vendor: composer.lock composer.phar
	./composer.phar install --prefer-dist
	@touch $@

composer.phar:
ifeq (, $(shell which composer 2>/dev/null))
	curl -fsSL 'https://getcomposer.org/installer' | php -- --filename=$@ --stable
else
	ln -s `which composer` $@
endif

config/components/web/request--cookie.php:
	bin/generate-secret > $@

.PHONY: config/params/git-revision.php
config/params/git-revision.php:
	bin/git-revision > $@

%.min.js: %.js node_modules
	npx terser --compress --mangle -o $@ $<

.PRECIOUS: %.js
%.js: %.es node_modules
	npx babel $< -o $@

%.min.css: %.css node_modules
	npx postcss $< --no-map --use cssnano -o $@
	@touch $@

.PRECIOUS: %.css node_modules
%.css: %.scss node_modules
	npx sass $< | npx postcss --no-map --use autoprefixer -o $@
	@touch $@

%.gz: %
	gzip --stdout --keep --no-name --quiet --best $< > $@

resources/css/dropdown-toggle.css: resources/css/dropdown-toggle.scss resources/css/_bootstrap-icons.scss node_modules

resources/css/_bootstrap-icons.scss: vendor
	./yii bootstrap-icons/convert > $@

.PHONY: messages
messages: vendor
	./yii message/extract @app/messages/config.php

%.po: messages

%.mo: %.po
	msgfmt -o $@ $<

web/favicon: node_modules
	cp -r $</@jp3cki/fetus.css/dist/favicon/ $@/

web/favicon.ico: web/favicon
	cp $</favicon.ico $@

bin/dep: bin/dep.sha1sum.txt
	curl -fsSL -o $@ 'https://deployer.org/releases/v6.8.0/deployer.phar'
	chmod +x $@
	cd $(dir $@) && sha1sum -c $(notdir $<)
