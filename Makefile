
bin/proxy.phar: src
	mkdir -p ./bin
	php -c . create-phar.php
