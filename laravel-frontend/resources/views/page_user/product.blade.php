@extends('layouts.user')
@section('title', 'Tất cả Sản phẩm')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endsection

@section('content')

<section class="user-section">
    <div class="user-container">
        
        <h2 class="user-section-title">
            Tất cả sản phẩm
        </h2>
        
        <div class="product-grid">
            @foreach($danhSachSanPham as $sp)
                @include('components.product-card', ['sp' => $sp])
            @endforeach
        </div>

        <div class="mt-12 flex justify-center w-full">
            {{ $danhSachSanPham->links() }}
        </div>

    </div>
</section>

@endsection