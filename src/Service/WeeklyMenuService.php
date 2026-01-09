<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Service;

use Shyguy\FoodChoiceCore\DTO\FoodSummary;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;

final class WeeklyMenuService
{
  public function __construct(private RepositoryInterface $repository) {}

  /**
   * Génère un menu pour 7 jours (Monday..Sunday).
   * Évite les répétitions de catégories consécutives et priorise les plats
   * non consommés depuis au moins `$notEatenSinceDays` jours.
   *
   * @param int $notEatenSinceDays
   * @return array<string, ?FoodSummary>
   */
  public function generateWeeklyMenu(int $notEatenSinceDays = 14): array
  {
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    $since = new \DateTimeImmutable(sprintf('-%d days', $notEatenSinceDays));

    $recent = $this->repository->findRecentCookedFoods($since);
    $recentIds = [];
    foreach ($recent as $cooked) {
      $recentIds[$cooked->getFoodId()] = true;
    }

    $available = [];
    foreach ($this->repository->findAllFoods() as $food) {
      if (!isset($recentIds[$food->getId()])) {
        $available[$food->getId()] = $food;
      }
    }

    $menu = [];
    $prevCategory = null;

    foreach ($days as $day) {
      $picked = null;

      foreach ($available as $id => $food) {
        $category = $food->getCuisine()?->getName() ?? null;
        if ($category === $prevCategory) {
          continue;
        }
        $picked = $food;
        unset($available[$id]);
        $prevCategory = $category;
        $menu[$day] = new FoodSummary($food->getId(), $food->getName(), $category);
        break;
      }

      if (!array_key_exists($day, $menu)) {
        $menu[$day] = null;
        $prevCategory = null;
      }
    }

    return $menu;
  }
}
