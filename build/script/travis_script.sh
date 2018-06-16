#!/usr/bin/env bash
set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# run static php-cs-fixer code analysis
./vendor/bin/php-cs-fixer fix --dry-run --diff --verbose
## because of Skipped installation of bin codecept for package codeception/codeception: file not found in package in 7.2
if [[ $(phpenv version-name) == '7.2' ]]; then wget http://codeception.com/codecept.phar | chmod +x codecept.phar ; fi
## run the tests with no coverage
if [[ $RUN_WITH_COVERAGE != 'true' && $(phpenv version-name) != '7.2' ]]; then composer run-tests; fi
if [[ $RUN_WITH_COVERAGE != 'true' && $(phpenv version-name) == '7.2' ]]; then composer run-tests-72; fi

## run the tests with no coverage
## enable xdebug again
./vendor/bin/codecept build
if [[ $RUN_WITH_COVERAGE == 'true' ]]; then mv ~/.phpenv/versions/$(phpenv version-name)/xdebug.ini.bak ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini; fi
if [[ $RUN_WITH_COVERAGE == 'true' ]]; then composer build-coverage; fi
if [[ $RUN_WITH_COVERAGE == 'true' ]]; then bash <(curl -s https://codecov.io/bash) -Z ; fi
if [[ $RUN_WITH_COVERAGE == 'true' ]]; then php vendor/bin/codacycoverage clover build/logs/clover.xml ; fi
