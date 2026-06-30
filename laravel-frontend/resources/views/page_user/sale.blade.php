@extends('layouts.user')
@section('title', 'Sản phẩm Khuyến mãi')
@section('content')

<section class="user-section">
    <div class="user-container">
        
        <h2 class="user-section-title">
            Sản phẩm khuyến mãi
        </h2>
        
        <div class="product-grid">
            @foreach($danhSachKhuyenMai as $sp)
                @include('components.product-card', ['sp' => $sp])
            @endforeach
        </div>

    </div>
</section>

@endsection