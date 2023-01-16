FROM nginx:1.21-alpine

ARG NGINX_FILE=./docker/nginx/conf/nginx.conf
ARG CONF_FILE=./docker/nginx/conf/app.conf
ENV TZ=Europe/Madrid

RUN apk update && \
    apk add tzdata libc6-compat; \
    \
    cp /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone; \
    \
    rm /etc/nginx/conf.d/default.conf

COPY ${NGINX_FILE} /etc/nginx/nginx.conf
COPY ${CONF_FILE} /etc/nginx/conf.d/app.conf
COPY ./docker/nginx/entrypoint.sh /usr/local/bin/entrypoint

WORKDIR /spalopia

## add permissions for nginx user
RUN chown -R nginx:nginx /spalopia && chmod -R 755 /spalopia && \
        chmod +x /usr/local/bin/entrypoint && \
        chown -R nginx:nginx /var/cache/nginx && \
        chown -R nginx:nginx /var/log/nginx && \
        chown -R nginx:nginx /etc/nginx/conf.d
RUN touch /var/run/nginx.pid && \
        chown -R nginx:nginx /var/run/nginx.pid

USER nginx

COPY ./spalopia/public /spalopi/public

ENTRYPOINT [ "entrypoint" ]
CMD ["nginx", "-g", "daemon off;"]
