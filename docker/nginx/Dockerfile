FROM nginx:latest

RUN apt-get update \
	&& apt-get install openssl \
	&& rm -rf /var/lib/apt/lists/*

#WORKDIR /var/www/project/docker/nginx/certs
#RUN openssl req -subj '/CN=localhost' -x509 -newkey rsa:4096 -nodes -keyout /etc/nginx/certs/key.pem -out /etc/nginx/certs/cert.pem -days 365
