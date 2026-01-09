<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Domain;

final class Food
{
  private string $id;
  private string $name;
  private ?Cuisine $cuisine;
  private array $ingredients;

  public function __construct(string $id, string $name, ?Cuisine $cuisine = null, array $ingredients = [])
  {
    $this->id = $id;
    $this->name = $name;
    $this->cuisine = $cuisine;
    $this->ingredients = $ingredients;
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getCuisine(): ?Cuisine
  {
    return $this->cuisine;
  }

  /** @return Ingredient[] */
  public function getIngredients(): array
  {
    return $this->ingredients;
  }
}
