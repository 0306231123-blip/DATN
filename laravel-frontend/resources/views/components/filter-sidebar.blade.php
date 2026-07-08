@php
    $selectedCategories = request()->get('danh_muc') ? explode(',', request()->get('danh_muc')) : [];
    $selectedBrands = request()->get('thuong_hieu') ? explode(',', request()->get('thuong_hieu')) : [];
@endphp

<!-- Sidebar Lọc -->
<div class="filter-sidebar">
    <div class="filter-main-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
        BỘ LỌC TÌM KIẾM
    </div>
    <form id="filter-form" action="{{ $actionUrl }}" method="GET">
        
        <!-- Sắp xếp -->
        <h3 class="filter-title">Sắp xếp</h3>
        <div class="mb-5">
            <select name="sort" class="w-full border-gray-300 text-xs rounded-sm shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 text-xs py-1.5 px-2 border">
                <option value="">Mặc định (Mới nhất)</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
            </select>
        </div>

        <!-- Khoảng giá -->
        <h3 class="filter-title mt-2">Khoảng giá</h3>
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-2">
                <input type="number" id="min_price_input" name="min_price" value="{{ request('min_price') }}" placeholder="TỪ" class="w-full px-2 py-1 border border-gray-300 text-xs rounded-sm text-sm focus:outline-none focus:border-pink-500">
                <span class="text-gray-500">-</span>
                <input type="number" id="max_price_input" name="max_price" value="{{ request('max_price') }}" placeholder="ĐẾN" class="w-full px-2 py-1 border border-gray-300 text-xs rounded-sm text-sm focus:outline-none focus:border-pink-500">
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
        <div class="flex flex-col gap-3 mb-5 pr-2">
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
        <div class="flex flex-col gap-3 mb-5 pr-2">
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

        <div class="mt-6 flex gap-2">
            <a href="{{ $actionUrl }}" class="w-1/2 flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-1.5 px-2 text-[11px] rounded-sm transition text-center uppercase shadow-sm">
                Xóa tất cả
            </a>
            <button type="submit" class="w-1/2 flex items-center justify-center bg-pink-500 hover:bg-pink-600 text-white font-medium py-1.5 px-2 text-[11px] rounded-sm transition uppercase shadow-sm">
                Áp dụng
            </button>
        </div>
    </form>
</div>
