@extends('layouts.app')

@section('styles')
    {{-- We can reuse the same forms.css stylesheet --}}
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <style>
        /* Add some specific styles for the image management section */
        .current-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .current-image-item {
            position: relative;
            aspect-ratio: 1 / 1;
        }
        .current-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
        .remove-image-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #DC2626;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-weight: bold;
            cursor: pointer;
            line-height: 24px;
            text-align: center;
        }
    </style>
@endsection

@section('content')

<div class="form-page-container">
    <div class="back-link-wrapper">
        <a href="{{ route('products.show', $product) }}" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            <span>Back to Product Page</span>
        </a>
    </div>
    <div class="form-wrapper">
        <h1 class="form-title">Edit Listing</h1>

         
        {{-- The main form now points to the 'products.update' route --}}
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH') {{-- This tells Laravel to treat it as an update request --}}

            <div class="form-group">
                <label for="product_name" class="form-label">Product Name</label>
                {{-- The value attribute is pre-filled with the existing product name --}}
                <input type="text" id="product_name" name="product_name" class="form-control" value="{{ old('product_name', $product->product_name) }}" required>
            </div>

            <div class="form-group">
                <label for="product_category" class="form-label">Category</label>
                <select id="product_category" name="product_category" class="form-control" required>
                    {{-- We use a loop to create the options and select the current one --}}
                    @php $categories = ['earrings', 'bracelets', 'rings', 'necklaces', 'other']; @endphp
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ old('product_category', $product->product_category) == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="product_price" class="form-label">Price</label>
                <input type="number" id="product_price" name="product_price" class="form-control" value="{{ old('product_price', $product->product_price) }}" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="product_description" class="form-label">Product Description</label>
                {{-- For textareas, the content goes between the tags --}}
                <textarea id="product_description" name="product_description" class="form-control" rows="5">{{ old('product_description', $product->product_description) }}</textarea>
            </div>
            
            <button type="submit" class="btn-submit">Save Changes</button>
        </form>

        <hr style="margin: 40px 0;">

        {{-- A separate section for managing images --}}
<div> {{-- A simple wrapper for the whole section --}}
    <h2 style="font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">Manage Photos</h2>
    
    {{-- Part 1: Current Photos --}}
    <div class="form-group">
        <label class="form-label">Current Photos</label>
        <div class="current-images-grid">
            @forelse ($product->images as $image)
                <div class="current-image-item">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Current product image">
                    <form action="{{ route('products.images.delete', ['product' => $product, 'image' => $image->image_id]) }}" 
                    method="POST" onsubmit="return confirm('Are you sure?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="remove-image-btn" title="Delete Image">×</button>
</form>
                </div>
            @empty
                <p>No images have been uploaded for this product yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Part 2: Add New Photos --}}
    <div class="form-group">
        <label for="product_images" class="form-label">Add New Photos</label>
        <form action="{{ route('products.images.add', $product) }}" 
        method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" id="product_images" name="product_images[]" class="form-control" accept="image/*" multiple>
            <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">You can select multiple images to add.</p>
            <button type="submit" class="btn-submit" style="margin-top: 15px;">Upload New Photos</button>
        </form>
    </div>
</div>

    </div>
</div>
@endsection