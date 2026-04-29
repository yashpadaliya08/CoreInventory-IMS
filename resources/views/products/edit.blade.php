@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-form-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .form-glass { background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; padding: 12px 16px; height: 48px; transition: all 0.2s; }
    .form-glass:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('products.show', $product) }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Profile</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Modify Master Profile</h2>
        </div>
    </div>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="edit-2" style="color: var(--primary);"></i> Core Product Telemetry</h6>
                    
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Product Name <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="box" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="name" class="form-control form-glass" style="padding-left: 42px;" value="{{ old('name', $product->name) }}" required>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">SKU Code <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="hash" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="sku" class="form-control form-glass text-uppercase" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" value="{{ old('sku', $product->sku) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Category Taxonomy</label>
                            <div class="position-relative">
                                <i data-feather="tag" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="category" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $catName)
                                        <option value="{{ $catName }}" {{ old('category', $product->category) == $catName ? 'selected' : '' }}>{{ $catName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Measurement Unit <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="aperture" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="unit_of_measure" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="Units" {{ old('unit_of_measure', $product->unit_of_measure) == 'Units' ? 'selected' : '' }}>Units (pcs)</option>
                                    <option value="Kg" {{ old('unit_of_measure', $product->unit_of_measure) == 'Kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                    <option value="Liters" {{ old('unit_of_measure', $product->unit_of_measure) == 'Liters' ? 'selected' : '' }}>Liters (L)</option>
                                    <option value="Boxes" {{ old('unit_of_measure', $product->unit_of_measure) == 'Boxes' ? 'selected' : '' }}>Boxes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mt-4 pt-3 border-top w-100">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Reorder Threshold <span class="text-danger">*</span></label>
                            <p class="text-muted small mb-2">Adjust the minimum inventory threshold alarm logic.</p>
                            <div class="position-relative" style="max-width: 300px;">
                                <i data-feather="alert-triangle" style="position: absolute; top: 18px; left: 14px; color: var(--text-main); width: 22px;"></i>
                                <input type="number" name="reorder_level" class="form-control form-glass" style="padding-left: 46px; height: 60px; font-size: 1.5rem; font-family: 'Outfit'; font-weight: 700; color: var(--primary);" value="{{ old('reorder_level', $product->reorder_level) }}" required min="0">
                            </div>
                        </div>

                        <div class="col-12 pt-3 border-top">
                            <h6 class="mb-3" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">💰 Pricing</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Unit Cost (₹)</label>
                            <div class="position-relative">
                                <i data-feather="trending-down" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="number" step="0.01" name="unit_cost" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" min="0" value="{{ old('unit_cost', $product->unit_cost) }}" placeholder="0.00">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">What you pay to purchase this product.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Selling Price (₹)</label>
                            <div class="position-relative">
                                <i data-feather="trending-up" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="number" step="0.01" name="selling_price" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" min="0" value="{{ old('selling_price', $product->selling_price) }}" placeholder="0.00">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">What you charge your customers.</small>
                        </div>
                    </div>

                        <div class="col-12 pt-3 border-top">
                            <h6 class="mb-3" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">🖼️ Product Image</h6>
                        </div>

                        <div class="col-12">
                            @if($product->image_path)
                            {{-- Current image strip --}}
                            <div id="currentImageWrap" class="d-flex align-items-center gap-3 p-3 mb-3" style="background: rgba(99,102,241,0.04); border-radius: 10px; border: 1px solid rgba(99,102,241,0.1);">
                                <img src="{{ $product->image_url }}" alt="Current Image" style="height: 72px; width: 72px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-bold" style="font-size: 0.85rem; color: var(--text-main);">Current product image</p>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">Upload a new file below to replace it.</p>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="removeImageCheck" name="remove_image" value="1" onchange="toggleRemoveImage(this)">
                                    <label class="form-check-label text-danger fw-bold" for="removeImageCheck" style="font-size: 0.8rem;">Remove Image</label>
                                </div>
                            </div>
                            @endif

                            <div id="imageDropZone" style="border: 2px dashed rgba(99,102,241,0.3); border-radius: 12px; padding: 32px; text-align: center; cursor: pointer; transition: all 0.2s; background: rgba(99,102,241,0.02);" onclick="document.getElementById('productImageInput').click()" ondragover="event.preventDefault(); this.style.borderColor='var(--primary)'; this.style.background='rgba(99,102,241,0.06)'" ondragleave="this.style.borderColor='rgba(99,102,241,0.3)'; this.style.background='rgba(99,102,241,0.02)'" ondrop="handleImageDrop(event)">
                                <div id="imagePreviewWrap" style="display: none;">
                                    <img id="imagePreview" src="" alt="Preview" style="max-height: 160px; max-width: 100%; border-radius: 8px; object-fit: contain; margin-bottom: 12px;">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); clearImage()">
                                        <i data-feather="trash-2" style="width: 14px;"></i> Remove
                                    </button>
                                </div>
                                <div id="imagePlaceholder">
                                    <i data-feather="upload-cloud" style="width: 40px; height: 40px; color: var(--primary); opacity: 0.5; margin-bottom: 12px;"></i>
                                    <p class="text-muted mb-1" style="font-weight: 600;">{{ $product->image_path ? 'Click to replace image' : 'Click or drag & drop an image here' }}</p>
                                    <p class="text-muted" style="font-size: 0.8rem;">JPEG, PNG, WebP — Max 2MB</p>
                                </div>
                            </div>
                            <input type="file" id="productImageInput" name="product_image" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewImage(this)">
                        </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2 w-100" style="height: 54px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                            <i data-feather="upload-cloud"></i> Push Network Updates
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewWrap').style.display = 'block';
            document.getElementById('imagePlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage() {
    document.getElementById('productImageInput').value = '';
    document.getElementById('imagePreview').src = '';
    document.getElementById('imagePreviewWrap').style.display = 'none';
    document.getElementById('imagePlaceholder').style.display = 'block';
}

function handleImageDrop(event) {
    event.preventDefault();
    const dropZone = document.getElementById('imageDropZone');
    dropZone.style.borderColor = 'rgba(99,102,241,0.3)';
    dropZone.style.background = 'rgba(99,102,241,0.02)';
    const files = event.dataTransfer.files;
    if (files.length > 0 && files[0].type.startsWith('image/')) {
        const input = document.getElementById('productImageInput');
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        previewImage(input);
    }
}

function toggleRemoveImage(checkbox) {
    const dropZone = document.getElementById('imageDropZone');
    if (checkbox.checked) {
        dropZone.style.opacity = '0.4';
        dropZone.style.pointerEvents = 'none';
        clearImage();
    } else {
        dropZone.style.opacity = '1';
        dropZone.style.pointerEvents = 'auto';
    }
}
</script>
@endpush
