<?php

if (!function_exists('storage_url')) {
   /**
    * Generate a URL to a file on the API server's storage.
    */
   function storage_url(?string $path): string
   {
      if (empty($path)) {
         return '';
      }
      return rtrim(config('services.api.url'), '/') . '/storage/' . ltrim($path, '/');
   }
}
