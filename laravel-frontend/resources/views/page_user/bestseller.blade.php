@extends('layouts.user')
@section('title', 'Sản phẩm Bán chạy')
@section('content')

@include('components.filter-assets')

<section class="user-section">
    <div class="user-container">
        
        <h2 class="user-section-title">
            Sản phẩm bán chạy
        </h2>
        
        <div class="product-layout">
            @include('components.filter-sidebar', ['actionUrl' => url('/user/bestseller')])

            <div class="product-main">
                <div class="product-grid">
                    @forelse($danhSachBanChay as $sp)
                        @include('components.product-card', ['sp' => $sp])
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-500 font-medium bg-white rounded-sm border border-gray-100" style="grid-column: 1 / -1;">
                            Không tìm thấy sản phẩm bán chạy nào phù hợp!
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-12 flex justify-center w-full">
                    {{ $danhSachBanChay->links() }}
                </div>
            </div>
        </div>

    </div>
</section>

@endsection