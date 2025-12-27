@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endsection

@section('content')
<div class="form-page-container">
    <a href="#" class="page-back-link" id="page-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back</span>
    </a>
    <div class="form-wrapper">
        <h1 class="form-title">Add a New Listing</h1>

{{-- =============================================== --}}
{{--   ADD THIS TEMPORARY DEBUGGING BLOCK            --}}
{{-- =============================================== --}}
@if ($errors->any())
    <div style="background-color: #fdd; border: 1px solid #f00; padding: 15px; margin-bottom: 20px;">
        <h4 style="margin-top: 0; font-weight: bold;">Whoops! Something went wrong.</h4>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    
    @csrf
    
    <div class="form-group">
        <label for="category" class="form-label">Category</label>
        <select id="category" name="product_category" class="form-control" required>
            <option value="" disabled selected>Select a Category</option>
            <option value="earrings">Earrings</option>
            <option value="bracelets">Bracelets</option>
            <option value="rings">Rings</option>
            <option value="necklaces">Necklaces</option>
            <option value="other">Other Jewelries</option>
        </select>
    </div>

    <div class="form-group">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Product Name" required>
    </div>

    <div class="form-group">
        <label for="price" class="form-label">Price</label>
        <input type="number" id="price" name="product_price" class="form-control" placeholder="$ Price (USD)" step="0.01" min="0" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">Product Description</label>
        <textarea id="description" name="product_description" class="form-control" rows="5" placeholder="Describe your item, materials used, dimensions, etc."></textarea>
    </div>

   <div class="form-group">
    <label for="product_images" class="form-label">Photos</label>
    {{-- 
        This is a standard file input. 
        - 'multiple' allows selecting more than one file.
        - The 'name' is 'product_images[]' to send an array of files.
        - We give it the .form-control class so it gets styled.
    --}}
    <input type="file" 
    id="product_images" 
    name="product_images[]" 
    class="form-control" accept="image/*" multiple required>
    
    {{-- We can add a small note for the user --}}
    <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">You can select multiple images at once.</p>
</div>
    
    <button type="submit" class="btn-submit">Add Listing</button>
</form>

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 8; i++) {
        const input = document.getElementById('photo-' + i);
        const preview = document.getElementById('preview-' + i);

        input.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url('${e.target.result}')`;
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.backgroundImage = '';
            }
        });
    }
});
</script>
@endsection