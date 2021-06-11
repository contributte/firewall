
.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

qa: phpstan cs

cs:
	vendor/bin/phpcs --standard=coding_style_ruleset.xml --extensions=php --encoding=utf-8 --cache=.phpcs-cache --colors -sp src

csf:
	vendor/bin/phpcbf --standard=coding_style_ruleset.xml --extensions=php --encoding=utf-8 --cache=.phpcs-cache --colors -sp src

phpstan:
	vendor/bin/phpstan analyse -l max -c phpstan.neon

tests:
	vendor/bin/tester -s -p php --colors 1 -C tests/Cases

coverage-clover:
	vendor/bin/tester -s -p phpdbg --colors 1 -C --coverage ./coverage.xml --coverage-src ./src ./tests/Cases

coverage-html:
	vendor/bin/tester -s -p phpdbg --colors 1 -C --coverage ./coverage.html --coverage-src ./src ./tests/Cases
