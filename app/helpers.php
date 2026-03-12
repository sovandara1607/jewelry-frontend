<?php

if (!function_exists('storage_url')) {
   /**
    * Generate a URL to a file on the API server.
    * - Paths starting with '/' (e.g. seeded /images/...) are served directly from public/.
    * - Relative paths (e.g. product-images/abc.jpg) are served from storage/.
    */
   function storage_url(?string $path): string
   {
      if (empty($path)) {
         return '';
      }
      $base = rtrim(config('services.api.url'), '/');
      if (str_starts_with($path, '/')) {
         return $base . $path;
      }
      return $base . '/storage/' . $path;
   }
}