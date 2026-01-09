<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Service;

use Shyguy\FoodChoiceCore\DTO\FoodSummary;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;

final class FoodSearchService
{
  public function __construct(private RepositoryInterface $repository) {}

  /**
   * Recherche par nom (partiel, insensible à la casse).
   *
   * @param string $query
   * @param int $limit
   * @return iterable<FoodSummary>
   */
  public function searchByName(string $query, int $limit = 10): iterable
  {
    $results = [];
    $q = trim($query);
    if ($q === '') {
      return [];
    }

    foreach ($this->repository->findAllFoods() as $food) {
      if (stripos($food->getName(), $q) !== false) {
        $results[] = new FoodSummary($food->getId(), $food->getName(), $food->getCuisine()?->getName() ?? null);
        if (count($results) >= $limit) {
          break;
        }
      }
    }

    return $results;
  }

  /**
   * Filtre par catégorie / cuisine (exact match).
   *
   * @param string $category
   * @param int $limit
   * @return iterable<FoodSummary>
   */
  public function findByCategory(string $category, int $limit = 20): iterable
  {
    $results = [];
    foreach ($this->repository->findAllFoods() as $food) {
      if ($food->getCuisine()?->getName() === $category) {
        $results[] = new FoodSummary($food->getId(), $food->getName(), $category);
        if (count($results) >= $limit) {
          break;
        }
      }
    }

    return $results;
  }

  /**
   * Retourne un plat aléatoire ou null si aucun.
   */
  public function getRandomFood(): ?FoodSummary
  {
    $all = [];
    foreach ($this->repository->findAllFoods() as $food) {
      $all[] = $food;
    }

    if (count($all) === 0) {
      return null;
    }

    $idx = array_rand($all);
    $food = $all[$idx];

    return new FoodSummary($food->getId(), $food->getName(), $food->getCuisine()?->getName() ?? null);
  }
}
