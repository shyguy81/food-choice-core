<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\DTO;

final class FoodSummary
{
  public string $id;
  public string $name;
  public ?string $category;
  public array $tags = [];

  public function __construct(string $id, string $name, ?string $category = null, array $tags = [])
  {
    $this->id = $id;
    $this->name = $name;
    $this->category = $category;
    $this->tags = $tags;
  }
}
