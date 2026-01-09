<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Service;

use Shyguy\FoodChoiceCore\DTO\FoodSummary;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;

final class SuggestionEngine
{
  private RepositoryInterface $repository;

  public function __construct(RepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  /**
   * Very small example algorithm: return the first food not eaten in the last X days.
   * The real implementation should be more sophisticated and test-covered.
   */
  public function suggestOne(int $notEatenSinceDays = 7): ?FoodSummary
  {
    $since = new \DateTimeImmutable(sprintf('-%d days', $notEatenSinceDays));
    $recent = $this->repository->findRecentCookedFoods($since);
    $recentIds = [];
    foreach ($recent as $cooked) {
      $recentIds[$cooked->getFoodId()] = true;
    }

    foreach ($this->repository->findAllFoods() as $food) {
      if (!isset($recentIds[$food->getId()])) {
        return new FoodSummary($food->getId(), $food->getName(), $food->getCuisine()?->getName() ?? null);
      }
    }

    return null;
  }
}
