<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
// use App\Http\Controllers\AuthController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;


// USER PROFILE & DASHBOARD
Route::get('/profile', [UserController::class, 'showProfile'])
    ->middleware(['auth']) // This line ensures only logged-in users can see this page
    ->name('profile');
Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
    ->middleware(['auth'])
    ->name('profile.avatar.update');
// new POST route for storing the shop
Route::post('/shops', [ShopController::class, 'store'])
    ->middleware('auth')
    ->name('shops.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
    ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
    // Your custom avatar update route
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
    ->name('profile.avatar.update');


    // SHOP MANAGEMENT
    // SHOP DASHBOARD
    Route::get('/my-shop', [ShopController::class, 'dashboard'])
    ->name('shops.dashboard');
    //The logic would be: After a user successfully creates a shop, the backend would redirect them to this shops.dashboard route.
    //create shop page
    Route::get('/shops/create', [ShopController::class, 'create'])
    ->name('shops.create');
    Route::get('/my-shop/edit', [ShopController::class, 'edit'])
    ->name('shops.edit');
    Route::patch('/my-shop', [ShopController::class, 'update'])
    ->name('shops.update');
    Route::patch('/my-shop/picture', [ShopController::class, 'updatePicture'])
    ->name('shops.picture.update');


    // PRODUCT LISTING MANAGEMENT
    // CREATING A PRODUCT
    Route::get('/products/create', [ProductController::class, 'create'])
    ->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])
    ->name('products.store');
    // FOR EDITING
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
    ->name('products.edit');
    Route::patch('/products/{product}', [ProductController::class, 'update'])
    ->name('products.update');
    // FOR DELETING A PRODUCT
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('products.destroy');
    // to handle adding new images to an existing product
    Route::post('/products/{product}/images', [ProductController::class, 'addImages'])
    ->name('products.images.add');
    // to handle deleting a specific image
    // Note: We need both the product and the image ID
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage'] )
    ->name('products.images.delete');


    // ADDING ITEMS TO THE CART
    Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])
    ->name('cart.remove');


    // ORDER PROCESSING
    Route::post('/order', [OrderController::class, 'store'])
    ->name('order.store');
    Route::get('/ordersuccess', [OrderController::class, 'success'])
    ->name('order.success');
    Route::post('/orders/accept/{orderItem}', [OrderController::class, 'accept'])
    ->name('orders.accept');
    Route::post('/orders/reject/{orderItem}', [OrderController::class, 'reject'])
    ->name('orders.reject');


});


Route::get('/', [HomeController::class, 'index']);
// This route points the homepage URL '/' to the 'index' method in HomeController
Route::get('/home', [HomeController::class, 'index'])
->name('home');

// Shop All page
Route::get('/shop', [ShopController::class, 'index'])
->name('shop.index');

// ROUTE for showing a single product
Route::get('/products/{product}', [ProductController::class, 'show'])
->name('products.show');
// ROUTE for the shopping cart
Route::get('/cart', [CartController::class, 'index'])
->name('cart.index');
// ROUTE for the public-facing shop page
Route::get('/shops/{handle}', [ShopController::class, 'showPublic'])
->name('shops.public');


require __DIR__ . '/auth.php';











//thank you page bruhhh finally this is over
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// authentication routes
// Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// User profile page
// Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');

// success pop up after creating a shop
// Route::get('/shops/create/success', [ShopController::class, 'showCreationSuccess'])->name('shops.success');