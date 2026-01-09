<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Tests;

use PHPUnit\Framework\TestCase;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;
use Shyguy\FoodChoiceCore\Domain\Food;
use Shyguy\FoodChoiceCore\Domain\Cuisine;
use Shyguy\FoodChoiceCore\Domain\CookedFood;
use Shyguy\FoodChoiceCore\Service\WeeklyMenuService;

class WeeklyMenuServiceTest extends TestCase
{
  public function testGeneratesSevenEntries(): void
  {
    $foods = [];
    for ($i = 1; $i <= 10; $i++) {
      $foods[] = new Food((string)$i, 'Food' . $i, new Cuisine('c' . $i, 'Cat' . $i));
    }

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

    $service = new WeeklyMenuService($repo);
    $menu = $service->generateWeeklyMenu(14);

    $this->assertCount(7, $menu);
    $ids = [];
    foreach ($menu as $day => $item) {
      $this->assertNotNull($item, "Expected a FoodSummary for $day");
      $ids[] = $item->id;
    }

    $this->assertCount(7, array_unique($ids));
  }

  public function testAvoidsConsecutiveCategories(): void
  {
    $foods = [
      new Food('1', 'A1', new Cuisine('c1', 'Alpha')),
      new Food('2', 'A2', new Cuisine('c1', 'Alpha')),
      new Food('3', 'B1', new Cuisine('c2', 'Beta')),
      new Food('4', 'C1', new Cuisine('c3', 'Gamma')),
      new Food('5', 'D1', new Cuisine('c4', 'Delta')),
      new Food('6', 'E1', new Cuisine('c5', 'Epsilon')),
      new Food('7', 'F1', new Cuisine('c6', 'Zeta')),
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

    $service = new WeeklyMenuService($repo);
    $menu = $service->generateWeeklyMenu(7);

    $prev = null;
    foreach ($menu as $day => $item) {
      if ($item === null) {
        $prev = null;
        continue;
      }
      $this->assertNotEquals($prev, $item->category, "Consecutive categories should differ on $day");
      $prev = $item->category;
    }
  }

  public function testHandlesLessThanSevenAndExcludesRecent(): void
  {
    $foods = [
      new Food('1', 'One', new Cuisine('c1', 'Alpha')),
      new Food('2', 'Two', new Cuisine('c2', 'Beta')),
      new Food('3', 'Three', new Cuisine('c3', 'Gamma')),
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
        return [new CookedFood('2', new \DateTimeImmutable('now'))];
      }
    };

    $service = new WeeklyMenuService($repo);
    $menu = $service->generateWeeklyMenu(7);

    $availableCount = 0;
    foreach ($menu as $item) {
      if ($item !== null) {
        $availableCount++;
        $this->assertNotEquals('2', $item->id);
      }
    }

    $this->assertLessThanOrEqual(3, $availableCount);
  }
}
