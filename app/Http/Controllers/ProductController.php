<?php

namespace App\Http\Controllers;

use App\Models\Product; // Import the Product model
use App\Models\Shop;    // Import the Shop model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Show the form for creating a new product listing.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Display a single product detail page.
     */
    public function show(Product $product)
    {

        $product->load('images');
        $seller = $product->shop; // Assumes a 'shop' relationship exists on the Product model

        return view('products.show', [
            'product' => $product,
            'seller' => $seller
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {

        // 1. Validate the incoming data
        // The keys must match the `name` attributes in your form
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:100',
            'product_category' => 'required|string|max:50',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string',
            'product_images' => 'required|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240'
        ]);

        // 2. Create the main product record using the correct column names
        $product = Product::create([
            'shop_id' => Auth::user()->shop->shop_id, // Use the correct primary key name
            'product_name' => $validatedData['product_name'],
            'product_category' => $validatedData['product_category'],
            'product_price' => $validatedData['product_price'],
            'product_description' => $validatedData['product_description'],
            'in_stock' => 1,
        ]);


        // 3. Handle the image uploads
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $path = $file->store('product-images', 'public');

                $product->images()->create([
                    'image_path' => $path,
                    'product_id' => $product->Product_id
                ]);
            }
        }

        // 4. Redirect the user
        return redirect()->route('shops.dashboard')->with('status', 'New listing added successfully!');
    }

    public function edit(Product $product)
    {
        // Authorization Check: Make sure the logged-in user owns this product
        if (Auth::user()->shop?->shop_id !== $product->shop_id) {
            abort(403, 'Unauthorized Action'); // Stop users from editing others' products
        }

        return view('products.edit', ['product' => $product]);
    }
    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Authorization Check
        if (Auth::user()->shop?->shop_id !== $product->shop_id) {
            abort(403, 'Unauthorized Action');
        }

        // Validate the incoming data (same as store, but 'unique' rule needs adjustment)
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:100',
            'product_category' => 'required|string|max:50',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string',
        ]);

        // Update the product
        $product->update($validatedData);

        return redirect()->route("shops.dashboard")->with('status', 'Listing updated successfully!');
    }
    /**
     * Add new images to an existing product.
     */
    public function addImages(Request $request, Product $product)
    {
        // Authorization check...

        $request->validate([
            'product_images' => 'required|array',
            'product_images.*' => 'image|max:2048'
        ]);

        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $path = $file->store('product-images', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return back()->with('status', 'New images added!');
    }

    /**
     * Delete a specific product image.
     */
    public function deleteImage(Product $product, \App\Models\ProductImage $image)
    {
        // Authorization check...

        // Delete the file from storage
        Storage::disk('public')->delete($image->image_path);

        // Delete the record from the database
        $image->delete();

        return back()->with('status', 'Image deleted!');
    }

    /**
 * Remove the specified product from storage.
 */
public function destroy(Product $product)
{
    // Authorization: Make sure the logged-in user owns this product
    if (Auth::user()->shop?->shop_id !== $product->shop_id) {
        abort(403, 'Unauthorized Action');
    }

    // Use a transaction to be safe
    DB::transaction(function () use ($product) {
        // 1. Delete the physical image files from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        // 2. Delete the product record from the database.
        //    Because of 'onDelete('cascade')' in your migration, this will also
        //    delete all related records from 'product_images' and 'orderitem'.
        $product->delete();
    });

    // Redirect to the seller's dashboard with a success message
    return redirect()->route('shops.dashboard')->with('status', 'Listing has been deleted.');
}
}