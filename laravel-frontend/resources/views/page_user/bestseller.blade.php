@extends('layouts.user')
@section('title', 'Sản phẩm Bán chạy')
@section('content')

<section class="user-section">
    <div class="user-container">
        
        <h2 class="user-section-title">
            Sản phẩm bán chạy
        </h2>
        
        <div class="product-grid">
            @foreach($danhSachBanChay as $sp)
                @include('components.product-card', ['sp' => $sp])
            @endforeach
        </div>

    </div>
</section>

@endsection