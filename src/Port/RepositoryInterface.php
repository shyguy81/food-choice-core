<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Port;

use Shyguy\FoodChoiceCore\Domain\CookedFood;
use Shyguy\FoodChoiceCore\Domain\Food;

interface RepositoryInterface
{
  /** @return iterable<Food> */
  public function findAllFoods(): iterable;

  /** @return iterable<CookedFood> */
  public function findRecentCookedFoods(\DateTimeImmutable $since): iterable;
}
