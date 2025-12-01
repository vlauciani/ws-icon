FROM webdevops/php-nginx:8.1

ENV WEB_DOCUMENT_ROOT=/app
#ENV PHP_DISMOD=bz2,calendar,exiif,ffi,intl,gettext,ldap,mysqli,imap,pdo_pgsql,pgsql,soap,sockets,sysvmsg,sysvsm,sysvshm,shmop,xsl,zip,gd,apcu,vips,yaml,imagick,mongodb,amqp

# Update NGINX GPG Key
RUN curl -fsSL https://nginx.org/keys/nginx_signing.key | apt-key add -

RUN apt-get update \
    && apt-get install -y \
        procps \
        iputils-ping \
    && rm -rf /var/lib/apt/lists/*

# Set .bashrc
RUN echo "" >> /root/.bashrc \
     && echo "##################################" >> /root/.bashrc \
     && echo "alias ll='ls -l --color'" >> /root/.bashrc \
     && echo "" >> /root/.bashrc \
     && echo "export LC_ALL=\"C\"" >> /root/.bashrc \
     && echo "" >> /root/.bashrc 

# Set 'root' pwd
RUN echo root:toor | chpasswd

USER application

# Set .bashrc
RUN echo "" >> /home/application/.bashrc \
     && echo "##################################" >> /home/application/.bashrc \
     && echo "alias ll='ls -l --color'" >> /home/application/.bashrc \
     && echo "" >> /home/application/.bashrc \
     && echo "export LC_ALL=\"C\"" >> /home/application/.bashrc \
     && echo "" >> /home/application/.bashrc

RUN mkdir /tmp/icons/ 
RUN mkdir /tmp/log/

WORKDIR /app
COPY --chown=1000:1000 . .
