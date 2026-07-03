@php
    $selectedCategories = request()->get('danh_muc') ? explode(',', request()->get('danh_muc')) : [];
    $selectedBrands = request()->get('thuong_hieu') ? explode(',', request()->get('thuong_hieu')) : [];
@endphp

<!-- Sidebar Lọc -->
<div class="filter-sidebar">
    <form id="filter-form" action="{{ $actionUrl }}" method="GET">
        
        <!-- Sắp xếp -->
        <h3 class="filter-title">Sắp xếp</h3>
        <div class="mb-5">
            <select name="sort" class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 text-sm py-2 px-3 border">
                <option value="">Mặc định (Mới nhất)</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
            </select>
        </div>

        <!-- Khoảng giá -->
        <h3 class="filter-title mt-2">Khoảng giá</h3>
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-2">
                <input type="number" id="min_price_input" name="min_price" value="{{ request('min_price') }}" placeholder="TỪ" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-pink-500">
                <span class="text-gray-500">-</span>
                <input type="number" id="max_price_input" name="max_price" value="{{ request('max_price') }}" placeholder="ĐẾN" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-pink-500">
            </div>
            <!-- Thanh kéo giá -->
            <div class="range-slider">
                <div class="slider-track"></div>
                <div class="slider-range-fill" id="slider-fill"></div>
                <input type="range" id="min_slider" min="0" max="5000000" step="50000" value="{{ request('min_price', 0) }}">
                <input type="range" id="max_slider" min="0" max="5000000" step="50000" value="{{ request('max_price', 5000000) }}">
            </div>
            <div class="text-xs text-gray-400 mt-1 text-center">(Kéo để chọn khoảng giá)</div>
        </div>

        <!-- Danh mục -->
        <h3 class="filter-title mt-2">Danh mục</h3>
        <div class="flex flex-col gap-3 mb-5 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
            @foreach($danhMucs as $dm)
            <div class="filter-item">
                <label class="filter-label">
                    <input type="checkbox" name="danh_muc_cb[]" value="{{ $dm->ma_danh_muc }}" class="filter-checkbox dm-checkbox" {{ in_array($dm->ma_danh_muc, $selectedCategories) ? 'checked' : '' }}>
                    {{ $dm->ten_danh_muc }}
                </label>
            </div>
            @endforeach
        </div>
        <input type="hidden" name="danh_muc" id="danh_muc_input" value="{{ request()->get('danh_muc') }}">

        <!-- Thương hiệu -->
        @if(isset($thuongHieus) && count($thuongHieus) > 0)
        <h3 class="filter-title mt-2">Thương hiệu</h3>
        <div class="flex flex-col gap-3 mb-5 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
            @foreach($thuongHieus as $th)
            <div class="filter-item">
                <label class="filter-label">
                    <input type="checkbox" name="thuong_hieu_cb[]" value="{{ $th }}" class="filter-checkbox th-checkbox" {{ in_array($th, $selectedBrands) ? 'checked' : '' }}>
                    {{ $th }}
                </label>
            </div>
            @endforeach
        </div>
        <input type="hidden" name="thuong_hieu" id="thuong_hieu_input" value="{{ request()->get('thuong_hieu') }}">
        @endif

        <button type="submit" class="mt-4 w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-xl transition shadow-md">
            Lọc Sản Phẩm
        </button>
        
        @if(request()->hasAny(['danh_muc', 'thuong_hieu', 'min_price', 'max_price', 'sort']))
        <a href="{{ $actionUrl }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-pink-500 underline">
            Xóa bộ lọc
        </a>
        @endif
    </form>
</div>
