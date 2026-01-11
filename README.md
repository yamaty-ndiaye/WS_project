# Comparateur de prix de boissons

## Objectif
Réaliser un comparateur de prix à partir de plusieurs sites e-commerce.

---

## Description du projet
Le projet compare les prix des boissons entre deux sites :
- Auchan
- Sakanal

Les données sont récupérées par web scraping, stockées dans une base de données, puis affichées via une interface web.

---

## Fonctionnement
- Scraping des produits (nom, prix, lien, image) avec Scrapy.
- Stockage des données dans une base MySQL.
- Base de données exécutée dans un conteneur Docker.
- Interface web (PHP) exécutée dans un conteneur Docker.
- Comparaison des prix entre les sites.
- Affichage du site proposant le prix le moins cher.
- Produits cliquables vers la page de vente.
- phpMyAdmin utilisé pour gérer la base de données.
- Orchestration des conteneurs avec Docker Compose.

---

## Conteneurs utilisés
- MySQL (base de données)
- Apache / PHP (interface web)
- Scrapy (scraping)
- phpMyAdmin (gestion de la base)

Chaque conteneur utilise son propre Dockerfile.

---

## Lancement du projet

```bash
docker-compose up -d --build
