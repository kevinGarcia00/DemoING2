# Creamos el dokerfile para que render pueda ejecutar la aplicacion con la misma configuracion que local.
# Le indicamos el php, instalamos mysqli, copia los archivos del backend y configuracion de puertos

# Indico levantarlo con php
FROM php:8.3-cli

# Instalar mysqli
RUN docker-php-ext-install mysqli

# Creo el directorio
WORKDIR /var/www/html

# Copio los archivos 
COPY . .

# Busco el router .php
RUN if [ ! -f /var/www/html/router.php ] && [ -f /var/www/html/backEnd/router.php ]; then \
		cp -a /var/www/html/backEnd/. /var/www/html/; \
	elif [ ! -f /var/www/html/router.php ]; then \
		echo 'router.php missing from Docker build context; copied files:'; \
		find /var/www/html -maxdepth 3 -type f -print; \
		exit 1; \
	fi \
	&& test -f /var/www/html/router.php

# Configuro puerto
ENV PORT=10000
EXPOSE 10000

# iniciar el servidor
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-10000} -t /var/www/html /var/www/html/router.php"]