FROM node:23.5.0

WORKDIR /var/www/html

RUN npm install npm --location=global

ENTRYPOINT [ "npm" ]
