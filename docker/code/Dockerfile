#this can be significantly simplified once this PR released https://github.com/moby/moby/pull/34263
FROM busybox

ARG PUID
RUN adduser -D -u ${PUID} u

COPY . /var/www
RUN chown -R ${PUID} /var/www/var
USER u
VOLUME /var/www
