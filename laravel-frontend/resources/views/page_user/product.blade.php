@extends('layouts.user')
@section('title', 'Tất cả Sản phẩm')

@include('components.filter-assets')

@section('content')

@php
    $selectedCategories = request()->get('danh_muc') ? explode(',', request()->get('danh_muc')) : [];
    $selectedBrands = request()->get('thuong_hieu') ? explode(',', request()->get('thuong_hieu')) : [];
@endphp

<section class="user-section" style="padding-top: 1.5rem;">
    <div class="user-container">
        
        <h2 class="user-section-title text-center mb-8">
            Tất cả sản phẩm
        </h2>
        
        <div class="product-layout">
            <!-- Sidebar Lọc -->
            @include('components.filter-sidebar', ['actionUrl' => url('/user/product')])

            <!-- Danh sách sản phẩm -->
            <div class="product-main">
                <div class="product-grid">
                    @forelse($danhSachSanPham as $sp)
                        @include('components.product-card', ['sp' => $sp])
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-500 font-medium bg-white rounded-sm border border-gray-100" style="grid-column: 1 / -1;">
                            Không tìm thấy sản phẩm nào trong danh mục này!
                        </div>
                    @endforelse
                </div>

                <div class="mt-12 flex justify-center w-full">
                    {{ $danhSachSanPham->links() }}
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
