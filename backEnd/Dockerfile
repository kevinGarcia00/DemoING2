FROM php:8.3-cli

RUN docker-php-ext-install mysqli

WORKDIR /var/www/html
COPY . .
RUN if [ ! -f /var/www/html/router.php ] && [ -f /var/www/html/backEnd/router.php ]; then \
		cp -a /var/www/html/backEnd/. /var/www/html/; \
	elif [ ! -f /var/www/html/router.php ]; then \
		echo 'router.php missing from Docker build context; copied files:'; \
		find /var/www/html -maxdepth 3 -type f -print; \
		exit 1; \
	fi \
	&& test -f /var/www/html/router.php

ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-10000} -t /var/www/html /var/www/html/router.php"]