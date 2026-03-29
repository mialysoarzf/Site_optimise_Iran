FROM php:8.3-apache

RUN apt-get update \
	&& apt-get install -y --no-install-recommends libpq-dev libonig-dev curl ca-certificates \
	&& docker-php-ext-install pdo_pgsql mbstring \
	&& a2enmod rewrite \
	&& apt-get purge -y --auto-remove \
	&& rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY docker/bootstrap-assets.sh /usr/local/bin/bootstrap-assets.sh
RUN chmod +x /usr/local/bin/bootstrap-assets.sh

CMD ["sh", "/usr/local/bin/bootstrap-assets.sh"]