<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// Debug route to see API responses
Route::get('/debug/api-response', function () {
   $apiUrl = config('services.api.url');
   $response = Http::get("{$apiUrl}/api/products/newest");

   if ($response->failed()) {
      return response()->json(['error' => 'API failed']);
   }

   $data = $response->json();
   $products = $data['products'] ?? [];

   $images = [];
   foreach ($products as $product) {
      if (isset($product['images'])) {
         foreach ($product['images'] as $image) {
            $images[] = [
               'product_name' => $product['product_name'],
               'image_path' => $image['image_path'] ?? null,
               'expected_url' => storage_url($image['image_path'] ?? null),
            ];
         }
      }
   }

   return response()->json([
      'total_products' => count($products),
      'total_images' => count($images),
      'sample_images' => array_slice($images, 0, 5),
      'all_images' => $images,
   ], 200, [], JSON_PRETTY_PRINT);
});
