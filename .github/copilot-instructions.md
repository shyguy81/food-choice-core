<!-- Copilot / AI agent instructions for the food-choice-core repository -->

# Instructions rapides pour les agents IA

Ce dépôt contient une petite librairie PHP (domaine) centrée sur la logique métier "FoodChoice".
L'objectif de ce fichier est de donner aux agents IA le contexte immédiatement utile pour contribuer rapidement.

- **Architecture principale :**

  - `src/Domain/` : entités métier immuables (ex. `Food`, `Cuisine`, `CookedFood`).
  - `src/Port/` : interfaces (adapters) attendues par le domaine (ex. `RepositoryInterface`, `ImageStorageInterface`).
  - `src/Service/` : orchestration / règles applicatives (ex. `SuggestionEngine`).
  - `src/DTO/` : objets de transfert (ex. `FoodSummary`).

- **Flux de données important :**

  - Les services (p. ex. `SuggestionEngine`) n'accèdent pas directement à la persistence : ils utilisent `RepositoryInterface`.
  - Exemple concret : `src/Service/SuggestionEngine.php` appelle `findRecentCookedFoods(DateTimeImmutable $since)` puis `findAllFoods()` et retourne un `FoodSummary`.

- **Conventions de développement observées :**

  - PHP >= 8.1, `declare(strict_types=1)` et typage fort partout (`composer.json`).
  - PSR-4 autoloading : namespace `Shyguy\\FoodChoiceCore\\` → `src/`.
  - Utilisation de `DateTimeImmutable` pour la logique temporelle.
  - Tests unitaires via PHPUnit ; les tests utilisent parfois des classes anonymes implémentant les ports (voir `tests/SuggestionEngineTest.php`).

- **Points d'intégration à connaître :**

  - Implémenter `RepositoryInterface` pour fournir les données du domaine (DB, mock, fixture).
  - Implémenter `ImageStorageInterface` si vous ajoutez des images/presigned URLs.

- **Commandes utiles / workflow local :**

  - Installer les dépendances : `composer install` (dans la racine du package).
  - Lancer les tests : `./vendor/bin/phpunit` (ou `./vendor/bin/phpunit tests/SuggestionEngineTest.php`).

- **Conseils pratiques pour les PRs**

  - Conserver la séparation `Domain` / `Port` / `Service` : ne déplacez pas la logique métier dans les adapters.
  - Pour ajouter de la fonctionnalité : ajoutez d'abord une interface dans `src/Port/` si besoin, puis une implémentation dans l'adapter externe (ou tests via classe anonyme).
  - Préférez retourner des DTO (`src/DTO/FoodSummary.php`) depuis les services plutôt que des entités enrichies quand l'API est orientée consommation.

- **Exemples à consulter**
  - `src/Service/SuggestionEngine.php` — algorithme d'exemple et contrats utilisés.
  - `src/Port/RepositoryInterface.php` — méthodes nécessaires pour les tests et le moteur de suggestion.
  - `tests/SuggestionEngineTest.php` — pattern de test : adapter anonyme pour `RepositoryInterface`.

Si une section est ambiguë ou si vous voulez que j'ajoute des règles supplémentaires (formatage, nommage, CI), dites-le et j'itérerai.
