<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Domain;

final class CookedFood
{
  private string $foodId;
  private \DateTimeImmutable $cookedAt;

  public function __construct(string $foodId, \DateTimeImmutable $cookedAt)
  {
    $this->foodId = $foodId;
    $this->cookedAt = $cookedAt;
  }

  public function getFoodId(): string
  {
    return $this->foodId;
  }

  public function getCookedAt(): \DateTimeImmutable
  {
    return $this->cookedAt;
  }
}
