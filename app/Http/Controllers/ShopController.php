<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    //  Display the main shop page with all products.

    public function index(Request $request)
    {
        // Start with the Product model
        $query = Product::query();

        // Join the 'shop' table to make its columns available for searching
        $query->join('shop', 'product.shop_id', '=', 'shop.shop_id');

        // Only select columns from the 'product' table to avoid conflicts
        $query->select('product.*');

        // 1. Apply Search Filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('product.product_name', 'like', $searchTerm)
                    ->orWhere('product.product_description', 'like', $searchTerm)
                    ->orWhere('product.product_category', 'like', $searchTerm)
                    ->orWhere('shop.shop_name', 'like', $searchTerm);
            });
        }

        // 2. Apply "Exclude Own Products" Filter
        if (Auth::check() && Auth::user()->shop) {
            $query->where('product.shop_id', '!=', Auth::user()->shop->shop_id);
        }

        // 3. Apply Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('product.product_category', $request->category);
        }

        // 4. Apply In-Stock Filter
        // $query->where('product.in_stock', '>', 0);
        // NEW LOGIC: Order by stock status first, then by other criteria
        // This will put all products with 'in_stock > 0' at the top,
        // and all products with 'in_stock = 0' at the bottom.
        $query->orderBy('in_stock', 'desc');

        // 5. Apply Sorting
        if ($request->get('sort') === 'price_asc') {
            $query->orderBy('product.product_price', 'asc');
        } elseif ($request->get('sort') === 'price_desc') {
            $query->orderBy('product.product_price', 'desc');
        } else {
            $query->orderBy('product.date_created', 'desc'); // Use orderBy instead of latest here
        }

        // 6. Eager load relationships for the final results
        $query->with('images', 'shop');

        // 7. Finally, execute the fully built query
        $products = $query->get();

        return view('shopall', ['products' => $products]);
    }
    /**
     * Show the form for creating a new shop.
     */
    public function create()
    {
        return view('shops.create');
    }

    /**  Display the dashboard for the user's own shop.
     */
    public function dashboard()
    {
        $shop = Auth::user()->shop;
        if (!$shop) {
            return redirect()->route('shops.create')->with('info', 'You need to create a shop first!');
        }

        $productIds = $shop->products()->pluck('product_id');

        // Get Active Listings
        $activeListings = $shop->products()
            ->where('in_stock', '>', 0)
            ->with('images')
            ->latest('date_created')
            ->get();

        // Get Pending Order Items
        $pendingOrders = \App\Models\OrderItem::whereIn('product_id', $productIds)
            ->whereHas('order', fn($q) => $q->where('status', 'Pending'))
            ->with(['product.images', 'order.user'])
            ->latest('date_created')
            ->get();

        // Get Confirmed Order Items
        $confirmedOrders = \App\Models\OrderItem::whereIn('product_id', $productIds)
            ->whereHas('order', fn($q) => $q->where('status', 'Confirmed'))
            ->with(['product.images', 'order.user'])
            ->latest('date_created')
            ->get();

        return view('shops.dashboard', [
            'shop' => $shop,
            'listings' => $activeListings,
            'pendingOrders' => $pendingOrders,
            'confirmedOrders' => $confirmedOrders,
        ]);
    }

    /**
     * Display a public-facing shop page for a given seller.
     */
    public function showPublic($handle)
    {
        // 1. Find the shop in the database by its name.
        //    We replace spaces in the URL ('%20') back into real spaces.
        $shopName = str_replace('%20', ' ', $handle);
        $shop = Shop::where('shop_name', $shopName)->firstOrFail();

        // Fetch the products for this shop
        $productsQuery = $shop->products()->with('images');
        
        //   NEW LOGIC: Order by stock status first, then by newest
        $productsQuery->orderBy('in_stock', 'desc')->latest('date_created');

        // Execute the query
        $products = $productsQuery->get();

        // We pass the REAL shop and its REAL products to the view.
        return view('shops.sellerpage', [
            'seller' => $shop,     // Use 'seller' to match the variable name in your view
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validate the incoming data.
        // The keys here must match the 'name' attributes in our form.
        $validatedData = $request->validate([
            'shop_name' => 'required|string|max:100|unique:shop,shop_name',
            'shop_email' => 'required|email|max:100',
            'shop_phonenumber' => 'required|string|max:20',
            'shop_address' => 'required|string|max:255',
            'shop_description' => 'nullable|string',
        ]);

        // 2. Add the current user's ID to the data
        $validatedData['user_id'] = Auth::id();

        // 3. Create the shop in the database.
        // This now works directly because the validated keys match the $fillable array.
        Shop::create($validatedData);

        // 4. Redirect the user to their new shop dashboard
        return redirect()->route('shops.dashboard')->with('status', 'Shop created successfully!');
    }

    public function edit()
    {
        // Get the currently authenticated user's shop
        $shop = Auth::user()->shop;

        // Pass the shop data to the view
        return view('shops.edit', ['shop' => $shop]);
    }

    /**
     * Update the shop information in the database.
     */
    public function update(Request $request): RedirectResponse
    {
        // Get the current user's shop
        $shop = Auth::user()->shop;

        // Validate the incoming data
        // The 'unique' rule is adjusted to ignore the current shop's name
        $validatedData = $request->validate([
            'shop_name' => 'required|string|max:100|unique:shop,shop_name,' . $shop->shop_id . ',shop_id',
            'shop_email' => 'required|email|max:100',
            'shop_phonenumber' => 'required|string|max:20',
            'shop_address' => 'required|string|max:255',
            'shop_description' => 'nullable|string',
        ]);

        // Update the shop with the validated data
        $shop->update($validatedData);

        // Redirect back to the edit page with a success message
        return redirect()->route('shops.dashboard')->with('status', 'Shop details updated successfully!');
    }
    public function updatePicture(Request $request): RedirectResponse
    {
        // 1. Validate the uploaded file
        $request->validate([
            'shop_profilepic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // e.g., must be an image, max 2MB
        ]);

        // 2. Get the user's shop
        $shop = $request->user()->shop;

        // 3. Delete the old picture if it exists
        if ($shop->shop_profilepic) {
            Storage::disk('public')->delete($shop->shop_profilepic);
        }

        // 4. Store the new picture and get its path
        // It will be stored in 'storage/app/public/shop-pictures'
        $path = $request->file('shop_profilepic')->store('shop-pictures', 'public');

        // 5. Update the shop's record in the database
        $shop->update(['shop_profilepic' => $path]);

        // 6. Redirect back to the shop dashboard with a success message
        return redirect()->route('shops.dashboard')->with('status', 'Shop picture updated successfully!');
    }
}