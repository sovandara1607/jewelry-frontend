<?php

if (!function_exists('storage_url')) {
   /**
    * Generate a URL to a file on the API server.
    * All user-uploaded images are now properly stored on the API server.
    */
   function storage_url(?string $path): string
   {
      if (empty($path)) {
         return '';
      }

      if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
         return $path;
      }

      $base = rtrim(config('services.api.url'), '/');

      // Static assets in public directory (e.g. /images/herobg.jpg)
      if (str_starts_with($path, '/')) {
         return $base . $path;
      }

      // User-uploaded files in storage (e.g. product-images/abc.jpg)
      return $base . '/storage/' . ltrim($path, '/');
   }
}