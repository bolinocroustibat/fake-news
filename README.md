# le générateur de fake news d'extrême-droite

## Dépendances

- php >= 8
- SQlite3

## Mettre à jour la base de données SQLite depuis le Google Sheet

Naviguer sur `/test.php?refresh` pour forcer la mise à jour des fichiers JSON

ou

```php
php utils/import_data.php
```

## Importer l'ancien fichier JSON des projets générés dans la base de données SQLite

```php
php utils/import_generated.php
```