# Food Choice Core

Librairie core contenant la logique métier (domain entities, ports, services) pour l'application FoodChoice.

But: extraire la logique réutilisable (suggestion, planification, règles) et la publier via Composer.

Installation (dev local):

1. Depuis la racine du projet, installez les dépendances du package:

```bash
cd packages/food-choice-core
composer install
```

2. Utilisation minimale:

```php
use Shyguy\FoodChoiceCore\Service\SuggestionEngine;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;

// Injecter un adapter implémentant RepositoryInterface
$repo = ...;
$engine = new SuggestionEngine($repo);
$suggestion = $engine->suggestOne();
```

Contrib: suivez les conventions PSR-12 et écrivez des tests unitaires.
