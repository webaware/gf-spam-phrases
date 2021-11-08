PKG_NAME			= gf-spam-phrases
PKG_VERSION			= $(shell sed -rn 's/^Version: (.*)/\1/p' $(PKG_NAME).php)

I18N_EMAIL			= translate@webaware.com.au
I18N_TEAM			= WebAware <$(I18N_EMAIL)>
I18N_HOME			= https://translate.webaware.com.au/glotpress/projects/gf-spam-phrases/

ZIP					= .dist/$(PKG_NAME)-$(PKG_VERSION).zip
FIND_PHP			= find . -path ./vendor -prune -o -path ./node_modules -prune -o -name '*.php'
LINT_PHP			= $(FIND_PHP) -exec php -l '{}' \; >/dev/null
SNIFF_PHP			= vendor/bin/phpcs -ps
SRC_PHP				= $(shell $(FIND_PHP) -print)

all:
	@echo please see Makefile for available builds / commands

.PHONY: all lint lint-php sniff-php zip changelog

# release product

zip: $(ZIP)

$(ZIP): $(SRC_PHP) images/* languages/* changelog.md
	$(LINT_PHP)
	$(SNIFF_PHP)
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

# code linters

lint: lint-php sniff-php

lint-php:
	@echo PHP lint...
	@$(LINT_PHP)

sniff-php:
	@echo PHP code sniffer...
	@$(SNIFF_PHP)
