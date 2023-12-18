PKG_NAME			:= gf-spam-phrases
PKG_VERSION			:= $(shell sed -rn 's/^Version: (.*)/\1/p' $(PKG_NAME).php)

I18N_EMAIL			:= translate@webaware.com.au
I18N_TEAM			:= WebAware <$(I18N_EMAIL)>
I18N_HOME			:= https://translate.webaware.com.au/glotpress/projects/gf-spam-phrases/

ZIP					:= .dist/$(PKG_NAME)-$(PKG_VERSION).zip
FIND_PHP			:= find . -path ./vendor -prune -o -path ./node_modules -prune -o -path './.*' -o -name '*.php'
SRC_PHP				:= $(shell $(FIND_PHP) -print)

all:
	@echo please see Makefile for available builds / commands

.PHONY: all lint lint-php zip changelog pot

# release product

zip: $(ZIP)

$(ZIP): $(SRC_PHP) static/images/* languages/* changelog.md
	rm -rf .dist
	mkdir .dist
	git archive HEAD --prefix=$(PKG_NAME)/ --format=zip -9 -o $(ZIP)

# changelog HTML for copying to the website changelog

changelog: /tmp/c.html

/tmp/c.html: changelog.md
	pandoc -f markdown-auto_identifiers -o $@ $<

# translations

pot: languages/$(PKG_NAME).pot

languages/$(PKG_NAME).pot: $(SRC_PHP)
	wp i18n make-pot . --skip-js --domain=$(PKG_NAME) \
		--exclude=.dist/.*,lib/.*,languages/.*,node_modules/.*,tests/.*,vendor/.* \
		--headers='{"X-Translation-Home":"$(I18N_HOME)","Report-Msgid-Bugs-To":"$(I18N_EMAIL)","Last-Translator":"$(I18N_TEAM)","Language-Team":"$(I18N_TEAM)"}'
	# need to commit new files before building .zip
	@false

# code linters

lint: lint-php

lint-php:
	@echo PHP lint...
	@$(FIND_PHP) -exec php7.4 -l '{}' \; >/dev/null
	@vendor/bin/phpcs -ps
	@vendor/bin/phpcs -ps --standard=phpcs-5.2.xml
