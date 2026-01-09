---
agent: agent
---

# Prompt VSCode Copilot — Food Choice Core Services (Option A)

Tu es un assistant de développement expert travaillant sur **food-choice-core**, une bibliothèque PHP découplée de logique métier (domaine + services).

## 📦 Contexte du projet core

- **Repo** : `shyguy81/food-choice-core`
- **Type** : Library PHP 8.1+ (aucune dépendance framework)
- **Architecture** : Domain-Driven Design léger avec ports/adapters
- **Structure actuelle** :
  ```
  src/
    Domain/        # Entités métier (Food, Cuisine, CookedFood, Ingredient)
    DTO/           # FoodSummary (transfert de données)
    Port/          # Interfaces (RepositoryInterface, ImageStorageInterface)
    Service/       # SuggestionEngine (algorithme de suggestion)
    Exception/     # CoreException
  tests/           # Tests PHPUnit
  ```
- **Service existant** : `SuggestionEngine` → suggère un plat non consommé récemment
- **Ports existants** :
  - `RepositoryInterface` : `findAllFoods()`, `findRecentCookedFoods(DateTimeImmutable $since)`
  - `ImageStorageInterface` : `generatePresignedUrl(string $key, int $ttl)`

## 🎯 Objectif

Ajouter **deux nouveaux services métier** au core pour centraliser la logique actuellement dispersée dans l'application consommatrice :

1. **`WeeklyMenuService`** : génération de menus hebdomadaires
2. **`FoodSearchService`** : recherche et filtrage de plats

Ces services doivent être **framework-agnostic**, testables unitairement, et utiliser uniquement les ports existants (ou les étendre si nécessaire).

---

## 📋 Tâches précises à réaliser

### 1. Créer `WeeklyMenuService`

**Fichier** : `src/Service/WeeklyMenuService.php`

**Responsabilités** :

- Générer un menu pour 7 jours (lundi → dimanche)
- Éviter les répétitions de catégories consécutives
- Prioriser les plats peu consommés récemment
- Retourner un tableau structuré `['Monday' => FoodSummary, ...]`

**Signature minimale** :

```php
final class WeeklyMenuService
{
  public function __construct(private RepositoryInterface $repository) {}

  /**
   * @return array<string, ?FoodSummary> Tableau associatif jour => plat
   */
  public function generateWeeklyMenu(int $notEatenSinceDays = 14): array;
}
```

**Algorithme suggéré** :

1. Récupérer tous les plats via `$repository->findAllFoods()`
2. Récupérer les plats récents via `$repository->findRecentCookedFoods()`
3. Filtrer les plats déjà consommés récemment
4. Pour chaque jour :
   - Sélectionner un plat aléatoire parmi ceux restants
   - Éviter la même catégorie (cuisine) que le jour précédent
   - Retirer le plat de la liste disponible
5. Retourner un tableau avec clés = jours de la semaine

**Jours attendus** : `['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']`

**DTO retourné** : `FoodSummary` (existant : id, name, category, tags)

---

### 2. Créer `FoodSearchService`

**Fichier** : `src/Service/FoodSearchService.php`

**Responsabilités** :

- Recherche de plats par nom (recherche partielle, insensible à la casse)
- Filtrage par catégorie/cuisine
- Limitation de résultats

**Signature minimale** :

```php
final class FoodSearchService
{
  public function __construct(private RepositoryInterface $repository) {}

  /**
   * Recherche par nom de plat (partiel, insensible à la casse)
   * @return iterable<FoodSummary>
   */
  public function searchByName(string $query, int $limit = 10): iterable;

  /**
   * Filtre par catégorie/cuisine
   * @return iterable<FoodSummary>
   */
  public function findByCategory(string $category, int $limit = 20): iterable;

  /**
   * Retourne un plat aléatoire
   */
  public function getRandomFood(): ?FoodSummary;
}
```

**Implémentation** :

- `searchByName()` : itérer sur `findAllFoods()`, filtrer avec `stripos($food->getName(), $query) !== false`
- `findByCategory()` : itérer sur `findAllFoods()`, filtrer par `$food->getCuisine()?->getName()`
- `getRandomFood()` : récupérer tous les plats, retourner un élément aléatoire via `array_rand()`

**Note performance** : Ces méthodes chargent tous les plats en mémoire. Si nécessaire, étendre `RepositoryInterface` pour ajouter des méthodes de filtrage côté adapter (implémentation future).

---

### 3. Étendre `RepositoryInterface` (optionnel, si besoin performance)

**Fichier** : `src/Port/RepositoryInterface.php`

Si les méthodes ci-dessus sont trop lentes (charge mémoire), ajouter :

```php
/** @return iterable<Food> */
public function searchFoodsByName(string $query, int $limit = 10): iterable;

/** @return iterable<Food> */
public function findFoodsByCategory(string $category, int $limit = 20): iterable;
```

⚠️ **Décision à prendre** : commencer sans étendre le port (implémentation simple), puis optimiser si besoin réel constaté.

---

### 4. Tests unitaires obligatoires

**Fichiers** :

- `tests/WeeklyMenuServiceTest.php`
- `tests/FoodSearchServiceTest.php`

**Couverture minimale** :

- `WeeklyMenuService` :
  - Génère bien 7 plats distincts
  - Évite les répétitions de catégories consécutives
  - Gère le cas où il y a moins de 7 plats disponibles
  - Exclut les plats récemment consommés
- `FoodSearchService` :
  - Recherche insensible à la casse
  - Respecte la limite de résultats
  - Filtrage par catégorie exact
  - Retourne null si aucun plat (getRandomFood)

**Pattern de test** : utiliser une classe anonyme implémentant `RepositoryInterface` (voir `tests/SuggestionEngineTest.php` comme exemple).

---

### 5. Documentation (PHPDoc + README)

- Ajouter docblocks complets sur les méthodes publiques :
  - Description claire
  - `@param` avec types
  - `@return` avec types
  - `@throws` si applicable
- Mettre à jour `README.md` du core avec exemples d'usage des nouveaux services

---

## 🛠️ Contraintes et bonnes pratiques

1. **Aucune dépendance externe** : uniquement PHP standard (pas de Symfony, Doctrine, etc.)
2. **Immutabilité** : objets Domain et DTO immutables
3. **Typage strict** : `declare(strict_types=1);` en haut de chaque fichier
4. **Final classes** : marquer les services `final` (pas d'héritage)
5. **Injection constructeur** : tous les services prennent leurs dépendances en constructeur
6. **Tests isolés** : pas de dépendances entre tests, utiliser des mocks/stubs
7. **Performance** : algorithmes O(n) ou O(n log n) acceptables pour < 1000 plats
8. **Lisibilité** : code simple et explicite, pas de sur-optimisation prématurée

---

## 📦 Livrables attendus

- `src/Service/WeeklyMenuService.php`
- `src/Service/FoodSearchService.php`
- `tests/WeeklyMenuServiceTest.php`
- `tests/FoodSearchServiceTest.php`
- Mise à jour `README.md` avec exemples d'usage
- (Optionnel) Extension de `RepositoryInterface` si justifiée

---

## 🧪 Comment exécuter / vérifier

Lancer les tests :

```bash
./vendor/bin/phpunit
# ou spécifique
./vendor/bin/phpunit tests/WeeklyMenuServiceTest.php
./vendor/bin/phpunit tests/FoodSearchServiceTest.php
```

Vérifier la couverture :

```bash
./vendor/bin/phpunit --coverage-text
```

---

## 🚫 Ne pas faire

- Ajouter des dépendances Composer (Symfony, Doctrine, etc.)
- Créer des méthodes non testées
- Mélanger logique métier et infrastructure
- Créer des dépendances circulaires entre services
- Utiliser des variables globales ou singletons
- Écrire des tests d'intégration nécessitant une base de données

---

## 💡 Exemple d'usage attendu (post-implémentation)

```php
// Dans l'application consommatrice (Symfony)
$weeklyMenu = $weeklyMenuService->generateWeeklyMenu(14);
foreach ($weeklyMenu as $day => $food) {
    echo "$day: " . ($food?->name ?? 'Pas de suggestion') . PHP_EOL;
}

$results = $searchService->searchByName('pizza', 5);
foreach ($results as $food) {
    echo $food->name . " (" . $food->category . ")" . PHP_EOL;
}

$random = $searchService->getRandomFood();
echo "Plat du jour : " . $random?->name ?? 'Aucun plat disponible';
```

---

## 📚 Références

- Architecture actuelle : `src/Service/SuggestionEngine.php`
- Pattern de test : `tests/SuggestionEngineTest.php`
- DTOs existants : `src/DTO/FoodSummary.php`
- Ports existants : `src/Port/RepositoryInterface.php`

Fin.
