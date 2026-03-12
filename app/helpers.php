<?php

if (!function_exists('storage_url')) {
   /**
    * Generate a URL to a file on the API server.
    * Handles various path formats from the API.
    */
   function storage_url(?string $path): string
   {
      if (empty($path)) {
         return '';
      }
      $base = rtrim(config('services.api.url'), '/');

      // If path already contains /storage/, use it directly
      if (str_contains($path, '/storage/')) {
         return $base . $path;
      }

      // If path starts with /images/ but has a hashed filename (uploaded file),
      // redirect to storage
      if (preg_match('#^/images/([A-Za-z0-9]{20,}\.\w+)$#', $path, $matches)) {
         return $base . '/storage/product-images/' . $matches[1];
      }

      // Static assets in public/ (e.g. /images/herobg.jpg)
      if (str_starts_with($path, '/')) {
         return $base . $path;
      }

      // Relative paths go to storage
      return $base . '/storage/' . $path;
   }
}