<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create()
    {
        return view('products.create');
    }

    public function show($product)
    {
        $apiUrl = config('services.api.url');
        $response = Http::get("{$apiUrl}/api/product/{$product}");

        if ($response->failed()) {
            abort(404);
        }

        $data = $response->json();
        $prod = (object) array_merge($data['product'], [
            'images' => collect($data['product']['images'] ?? [])->map(fn($i) => (object) $i),
        ]);
        $seller = $data['seller'] ? (object) $data['seller'] : null;

        return view('products.show', [
            'product' => $prod,
            'seller' => $seller,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:100',
            'product_category' => 'required|string|max:50',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string',
            'product_images' => 'required|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        // First, try creating product without images
        $response = Http::withToken($token)->post("{$apiUrl}/api/product", [
            'product_name' => $request->product_name,
            'product_category' => $request->product_category,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description ?? '',
        ]);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? [];
            if ($errors) {
                return back()->withErrors($errors)->withInput();
            }
            return back()->withErrors(['api' => 'Failed to create product: ' . $response->body()])->withInput();
        }

        $productData = $response->json();
        $productId = $productData['product']['product_id'] ?? null;

        if (!$productId) {
            return back()->withErrors(['api' => 'Product created but no ID returned'])->withInput();
        }

        // Upload images one by one - API might expect different field names
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $file) {
                $error = $this->uploadProductImage($apiUrl, $token, $productId, $file, $index);
                if ($error) {
                    return redirect()->route('shops.dashboard')
                        ->with('warning', 'Product created but image #' . ($index + 1) . ' failed: ' . $error);
                }
            }
        }

        return redirect()->route('shops.dashboard')->with('status', 'New listing added successfully!');
    }

    public function edit($product)
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->get("{$apiUrl}/api/product/{$product}/edit");

        if ($response->failed()) {
            abort(403, 'Unauthorized Action');
        }

        $data = $response->json();
        $prod = (object) array_merge($data['product'], [
            'images' => collect($data['product']['images'] ?? [])->map(fn($i) => (object) $i),
        ]);

        return view('products.edit', ['product' => $prod]);
    }

    public function update(Request $request, $product)
    {
        $request->validate([
            'product_name' => 'required|string|max:100',
            'product_category' => 'required|string|max:50',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->patch("{$apiUrl}/api/products/{$product}", [
            'product_name' => $request->product_name,
            'product_category' => $request->product_category,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['api' => 'Failed to update product.'])->withInput();
        }

        return redirect()->route('shops.dashboard')->with('status', 'Listing updated successfully!');
    }

    public function addImages(Request $request, $product)
    {
        $request->validate([
            'product_images' => 'required|array',
            'product_images.*' => 'image|max:2048',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $file) {
                $error = $this->uploadProductImage($apiUrl, $token, $product, $file, $index);
                if ($error) {
                    return back()->withErrors(['api' => 'Failed to add image #' . ($index + 1) . ': ' . $error]);
                }
            }
        }

        return back()->with('status', 'New images added!');
    }

    public function deleteImage($product, $image)
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->delete("{$apiUrl}/api/products/{$product}/images/{$image}");

        if ($response->failed()) {
            return back()->withErrors(['api' => 'Failed to delete image.']);
        }

        return back()->with('status', 'Image deleted!');
    }

    public function destroy($product)
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->delete("{$apiUrl}/api/products/{$product}");

        if ($response->failed()) {
            return back()->withErrors(['api' => 'Failed to delete product.']);
        }

        return redirect()->route('shops.dashboard')->with('status', 'Listing has been deleted.');
    }

    private function uploadProductImage(string $apiUrl, string $token, int $productId, $file, int $index = 0): ?string
    {
        $fileContents = file_get_contents($file->getRealPath());
        $fileName = $file->getClientOriginalName();
        $fields = [
            'image',
            'product_image',
            "product_images[{$index}]",
            'product_images[]',
        ];

        $lastMessage = null;

        foreach ($fields as $field) {
            $response = Http::withToken($token)
                ->attach($field, $fileContents, $fileName)
                ->post("{$apiUrl}/api/products/{$productId}/images");

            if ($response->ok()) {
                return null;
            }

            $lastMessage = $response->json('message') ?? $response->body();

            if ($response->status() !== 422) {
                return $lastMessage ?: 'Failed to upload image.';
            }
        }

        return $lastMessage ?: 'Failed to upload image.';
    }
}