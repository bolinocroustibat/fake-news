# le générateur de fake news d'extrême-droite

## Dépendances

- php >= 8
- SQlite3

## Mettre à jour les fichiers de cahe JSON depuis le Google Sheet

Naviguer sur `/test.php?refresh` pour forcer la mise à jour des fichiers JSON

ou

```php
php import_to_json.php
```

## Importer l'ancien fichier JSON des projets générés dans la base SQLite

```php
php import_to_sql.php
```