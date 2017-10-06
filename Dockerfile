FROM laradock/workspace:1.8-71

RUN groupadd -g 1000 ubuntu && \
    useradd -u 1000 -g ubuntu -m ubuntu

#https://github.com/moby/moby/issues/6119
COPY . /app/user/
USER root
RUN chown -R ubuntu:ubuntu /app/user/var/

USER ubuntu
WORKDIR /app/user

#RUN composer install #chown /vendor is painfully long
RUN bin/load-data

# Export heroku bin
ENV PATH /app/user/bin:$PATH

ENV PORT 8080
CMD php bin/console server:run 0.0.0.0:$PORT
