
bin/proxy.phar: src create-phar.php
	mkdir -p ./bin
	php -c . create-phar.php
