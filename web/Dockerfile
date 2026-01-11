FROM php:8.2-apache

# Installation de l'extension pour MySQL
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# On copie le contenu de ton dossier local 'web' dans le conteneur
COPY . /var/www/html/

# Port standard pour le serveur Web
EXPOSE 80