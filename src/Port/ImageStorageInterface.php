<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceCore\Port;

interface ImageStorageInterface
{
  /**
   * Generate a presigned URL for a stored image key.
   */
  public function generatePresignedUrl(string $key, int $ttl = 3600): string;
}
