<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Tests;

use PHPUnit\Framework\TestCase;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;
use Shyguy\FoodChoiceCore\Domain\Food;
use Shyguy\FoodChoiceCore\Domain\Cuisine;
use Shyguy\FoodChoiceCore\Domain\CookedFood;
use Shyguy\FoodChoiceCore\Service\SuggestionEngine;

class SuggestionEngineTest extends TestCase
{
  public function testSuggestOneReturnsFirstNonRecentFood(): void
  {
    $foods = [new Food('1', 'Pâtes', new Cuisine('c1', 'Italienne')), new Food('2', 'Salade', new Cuisine('c2', 'Salade'))];

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
        return [new CookedFood('1', new \DateTimeImmutable('now'))];
      }
    };

    $engine = new SuggestionEngine($repo);
    $suggestion = $engine->suggestOne(7);

    $this->assertNotNull($suggestion);
    $this->assertEquals('2', $suggestion->id);
  }
}
