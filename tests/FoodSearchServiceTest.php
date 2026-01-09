<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Tests;

use PHPUnit\Framework\TestCase;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;
use Shyguy\FoodChoiceCore\Domain\Food;
use Shyguy\FoodChoiceCore\Domain\Cuisine;
use Shyguy\FoodChoiceCore\Service\FoodSearchService;

class FoodSearchServiceTest extends TestCase
{
  public function testSearchByNameIsCaseInsensitiveAndRespectsLimit(): void
  {
    $foods = [
      new Food('1', 'Pizza Margherita', new Cuisine('c1', 'Italienne')),
      new Food('2', 'pizzetta', new Cuisine('c1', 'Italienne')),
      new Food('3', 'Salade César', new Cuisine('c2', 'Salade')),
    ];

    $repo = new class($foods) implements RepositoryInterface {
      private array $foods;
      public function __construct(array $foods)
      {
        $this->foods = $foods;
      }
      public function findAllFoods(): iterable
      {
        return $this->foods;
      }
      public function findRecentCookedFoods(\DateTimeImmutable $since): iterable
      {
        return [];
      }
    };

    $service = new FoodSearchService($repo);
    $results = $service->searchByName('piz', 5);
    $this->assertCount(2, is_countable($results) ? $results : iterator_to_array($results));
  }

  public function testFindByCategoryReturnsExactMatchesAndRespectsLimit(): void
  {
    $foods = [
      new Food('1', 'Pâtes', new Cuisine('c1', 'Italienne')),
      new Food('2', 'Risotto', new Cuisine('c1', 'Italienne')),
      new Food('3', 'Sushi', new Cuisine('c2', 'Japonaise')),
    ];

    $repo = new class($foods) implements RepositoryInterface {
      private array $foods;
      public function __construct(array $foods)
      {
        $this->foods = $foods;
      }
      public function findAllFoods(): iterable
      {
        return $this->foods;
      }
      public function findRecentCookedFoods(\DateTimeImmutable $since): iterable
      {
        return [];
      }
    };

    $service = new FoodSearchService($repo);
    $results = $service->findByCategory('Italienne', 1);
    $arr = is_countable($results) ? $results : iterator_to_array($results);
    $this->assertCount(1, $arr);
    $this->assertEquals('Italienne', $arr[0]->category);
  }

  public function testGetRandomFoodReturnsNullWhenEmpty(): void
  {
    $repo = new class() implements RepositoryInterface {
      public function findAllFoods(): iterable
      {
        return [];
      }
      public function findRecentCookedFoods(\DateTimeImmutable $since): iterable
      {
        return [];
      }
    };

    $service = new FoodSearchService($repo);
    $this->assertNull($service->getRandomFood());
  }
}
