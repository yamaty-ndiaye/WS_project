Projet : Comparateur de prix de boissons (Auchan vs Sakanal)
Ce projet est un outil de veille tarifaire automatisé permettant de comparer les prix des boissons entre deux enseignes sénégalaises. Il repose sur une architecture multi-conteneurs pour garantir une installation et une exécution rapide.

Objectifs du projet
Web Scraping : Collecte automatisée des données via Scrapy (noms, prix, images, liens).

Base de données : Stockage centralisé des informations dans MySQL.

Interface Web : Visualisation en PHP permettant d'identifier le site le moins cher.

Dockerisation : Utilisation de Docker Compose pour orchestrer l'ensemble des services.

Architecture Technique
Le projet utilise Docker Compose (version 3.8) pour piloter les services suivants :

db (mysql_boissons) : Base de données MySQL 8.0 avec volume persistant.

web (web_boissons) : Serveur Apache/PHP basé sur un Dockerfile personnalisé.

phpmyadmin (pma_boissons) : Interface graphique de gestion de la base de données.

Installation et Lancement
1. Démarrer les conteneurs
Depuis la racine du projet, lancez la construction et le démarrage des services :


docker-compose up -d --build
2. Alimenter la base de données (Scraping)
Lancez les spiders Scrapy pour récupérer les données actuelles des deux sites :


# Lancement pour Auchan
scrapy crawl auchan

# Lancement pour Sakanal
scrapy crawl sakanal
3. Accéder au comparateur
Une fois le scraping terminé, ouvrez votre navigateur à l'adresse suivante : http://localhost:8000

Fonctionnement du Comparateur
L'interface PHP traite les données pour offrir une expérience utilisateur claire :

Appairage automatique : Le code normalise les noms des produits pour comparer des articles similaires.

Duel de prix : Les produits sont affichés face à face pour une lecture immédiate.

Indicateur de gain : Un badge visuel désigne automatiquement le site proposant le prix le plus bas.

Liens cliquables : Chaque article renvoie directement vers la fiche produit originale pour l'achat.