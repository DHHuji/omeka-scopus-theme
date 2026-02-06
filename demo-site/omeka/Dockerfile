FROM erseco/alpine-omeka-s:latest

COPY --chmod=755 docker-entrypoint-init.d/ /docker-entrypoint-init.d/
COPY opcache-dev.ini /etc/php84/conf.d/99-opcache-dev.ini
