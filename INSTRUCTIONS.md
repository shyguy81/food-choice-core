# food-choice-core — Instructions de publication

Objectif

- Préparer le paquet `shyguy81/food-choice-core` pour publication sur GitHub/Packagist.

Fichiers clés

- `composer.json`: nom `shyguy81/food-choice-core`, `type: library`, `autoload` PSR-4 (ex: `Shyguy\\FoodChoiceCore\\` → `src/`), `license` (ex: MIT), `require` PHP `>=8.4`.
- Code source: dans `src/` avec namespace correspondant.

Versioning & branches

- Branch principale: `main` (alias `dev-main`).
- Utiliser tags sémantiques (`v1.0.0`, `v1.1.0`, ...).
- Option utile dans `composer.json`:
  ```json
  "extra": {
    "branch-alias": {
      "dev-main": "1.0-dev"
    }
  }
  ```

Publication sur GitHub + Packagist

1. Pousser le dépôt sur `git@github.com:shyguy81/food-choice-core.git`.
2. Aller sur https://packagist.org et vous connecter (ou utiliser l'intégration GitHub).
3. Ajouter le repository via l'URL GitHub (ou activer l'intégration automatique GitHub App).
4. À chaque push de tag `vX.Y.Z`, Packagist publie automatiquement la version.

CI / QA (GitHub Actions recommandé)

- Jobs recommandés:
  - `composer validate` (valide `composer.json`).
  - `composer install --no-dev --prefer-dist` puis `php -l` sur les fichiers PHP.
  - `composer test` ou `vendor/bin/phpunit --colors=always`.
  - `composer cs-check` si vous avez des règles de style (facultatif).

Exemple minimal d'étapes:

- `composer validate --strict`
- `composer install --prefer-dist --no-interaction`
- `vendor/bin/phpunit --testsuite unit`

Bonnes pratiques

- Garder `composer.json` minimal et cohérent (dépendances exactes).
- Fournir `README.md` avec usage, exemples et migration si nécessaire.
- Mettre un `CHANGELOG.md` et un fichier `VERSION` si utile.
- Ajouter un fichier `ISSUE_TEMPLATE.md` et `PULL_REQUEST_TEMPLATE.md`.

Local development

- Pour développement local dans le projet principal (`food_choice_app`), utiliser `path` repository dans `composer.json` du projet consommateur:
  ```json
  {
    "type": "path",
    "url": "../path/to/food-choice-core",
    "options": { "symlink": true }
  }
  ```

Dépannage rapide

- "Package not found on Packagist": vérifier le nom dans `composer.json` et que le repo GitHub est accessible.
- Erreur de casse namespace (Linux): vérifier que le namespace PSR-4 et le nom de la classe respectent la casse exacte.

Contact

- Mainteneur: `shyguy81` (GitHub).
