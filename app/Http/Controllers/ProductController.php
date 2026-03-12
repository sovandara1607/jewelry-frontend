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

        $multipart = [
            ['name' => 'product_name', 'contents' => $request->product_name],
            ['name' => 'product_category', 'contents' => $request->product_category],
            ['name' => 'product_price', 'contents' => (string) $request->product_price],
            ['name' => 'product_description', 'contents' => (string) ($request->product_description ?? '')],
        ];

        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $stream = fopen($file->getRealPath(), 'r');
                if ($stream === false) {
                    return back()->withErrors(['api' => 'Could not read image file.'])->withInput();
                }

                $multipart[] = [
                    'name' => 'product_images[]',
                    'contents' => $stream,
                    'filename' => $file->getClientOriginalName(),
                    'headers' => ['Content-Type' => $file->getMimeType()],
                ];
            }
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->asMultipart()
            ->post("{$apiUrl}/api/product", $multipart);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? [];
            if ($errors) {
                return back()->withErrors($errors)->withInput();
            }
            return back()->withErrors(['api' => 'Failed to create product: ' . $response->body()])->withInput();
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

        $resolvedProductId = $this->resolveProductId($apiUrl, $token, $product);

        if (!$request->hasFile('product_images')) {
            return back()->withErrors(['api' => 'Please select at least one image.']);
        }

        $multipart = [];

        foreach ($request->file('product_images') as $file) {
            $stream = fopen($file->getRealPath(), 'r');
            if ($stream === false) {
                return back()->withErrors(['api' => 'Could not read image file.']);
            }

            $multipart[] = [
                'name' => 'product_images[]',
                'contents' => $stream,
                'filename' => $file->getClientOriginalName(),
                'headers' => ['Content-Type' => $file->getMimeType()],
            ];
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->asMultipart()
            ->post("{$apiUrl}/api/products/{$resolvedProductId}/images", $multipart);

        if ($response->failed()) {
            $message = $response->json('message') ?? $response->body();
            return back()->withErrors(['api' => 'Failed to add images: ' . $message]);
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

    private function resolveProductId(string $apiUrl, string $token, $product)
    {
        $productId = $product;
        $response = Http::withToken($token)->acceptJson()->get("{$apiUrl}/api/product/{$product}");

        if ($response->ok()) {
            $payload = $response->json('product') ?? [];
            $resolved = $payload['product_id'] ?? $payload['id'] ?? null;
            if (!empty($resolved)) {
                $productId = $resolved;
            }
        }

        return $productId;
    }
}