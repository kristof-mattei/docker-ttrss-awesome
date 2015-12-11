FROM php:5-fpm

# install GIT
RUN DEBIAN_FRONTEND=noninteractive \
	apt-get update && \
	apt-get install -y git --no-install-recommends && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Pull TTRSS
RUN git clone https://tt-rss.org/gitlab/fox/tt-rss.git /var/www/html

# TODO
# Patch TTRSS

RUN echo "" >> /usr/local/etc/php-fpm.conf && \
    echo "php_flag[display_errors] = on" >> /usr/local/etc/php-fpm.conf && \
    echo "php_flag[display_startup_errors] = on" >> /usr/local/etc/php-fpm.conf && \
    echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.conf


VOLUME /var/www/html