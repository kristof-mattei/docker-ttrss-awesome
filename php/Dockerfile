FROM php:5-fpm

# install GIT
RUN DEBIAN_FRONTEND=noninteractive \
	apt-get update && \
	apt-get install -y git --no-install-recommends && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Pull TTRSS, this is part of the build process
# I might move this to only at runtime, still need to see
# how good/bad this is
RUN git clone https://tt-rss.org/gitlab/fox/tt-rss.git /var/www/html

# Patch PHP config so that we get errors
RUN echo "" >> /usr/local/etc/php-fpm.conf && \
    echo "php_flag[display_errors] = on" >> /usr/local/etc/php-fpm.conf && \
    echo "php_flag[display_startup_errors] = on" >> /usr/local/etc/php-fpm.conf && \
    echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.conf

ENV DB_HOST ttrss
ENV DB_PORT 5432
ENV DB_NAME ttrss
ENV DB_USERNAME ttrss
ENV DB_PASSWORD ttrss

VOLUME /var/www/html

ADD patch.sh            /srv/patch.sh
ADD apply-schema.php    /srv/apply-schema.php

CMD ["/srv/patch.sh"]
