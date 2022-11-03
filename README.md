# epsi-sn2-tp-final
Projet vu en cours terminé

# Installer et démarrer le projet en local

- Télécharger le projet
- Démarrer le serveur SQL (MySQL ou MariaDB)
- `composer install`
- `symfony console doctrine:database:create`
- `symfony server:start`


# Mise à jour de la base de données

Après avoir effectué des modifications sur les entités (dans `./src/Entity`) :

- `symfony console make:migration`
- `symfony console doctrine:migrations:migrate` ou le raccourci `symfony console d:m:m`
