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

Exemples d'utilisation des nouveaux services:

```php
use Shyguy\FoodChoiceCore\Service\WeeklyMenuService;
use Shyguy\FoodChoiceCore\Service\FoodSearchService;

$repo = /* implémentation de RepositoryInterface */;

$weekly = new WeeklyMenuService($repo);
$menu = $weekly->generateWeeklyMenu(14);
foreach ($menu as $day => $food) {
	echo $day . ': ' . ($food?->name ?? 'Pas de suggestion') . "\n";
}

$search = new FoodSearchService($repo);
foreach ($search->searchByName('pizza', 5) as $f) {
	echo $f->name . " ({$f->category})\n";
}
```

Contrib: suivez les conventions PSR-12 et écrivez des tests unitaires.
