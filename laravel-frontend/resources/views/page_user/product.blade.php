@extends('layouts.user')
@section('title', 'Tất cả Sản phẩm')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<style>
    .product-layout {
        display: flex;
        gap: 30px;
        align-items: flex-start;
    }
    .filter-sidebar {
        width: 250px;
        flex-shrink: 0;
        background: #fefcf8;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #fce7f3;
        position: sticky;
        top: 20px;
    }
    .product-main {
        flex-grow: 1;
    }
    .filter-title {
        font-weight: 900;
        color: #1f2937;
        margin-bottom: 15px;
        font-size: 1.1rem;
        text-transform: uppercase;
        border-bottom: 2px dashed #fce7f3;
        padding-bottom: 10px;
    }
    .filter-item {
        margin-bottom: 10px;
    }
    .filter-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        color: #4b5563;
        font-weight: 500;
        transition: all 0.2s;
    }
    .filter-label:hover {
        color: #ec4899;
    }
    .filter-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        accent-color: #ec4899;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .product-layout {
            flex-direction: column;
        }
        .filter-sidebar {
            width: 100%;
            position: static;
        }
    }
</style>
@endsection

@section('content')

@php
    $selectedCategories = request()->get('danh_muc') ? explode(',', request()->get('danh_muc')) : [];
@endphp

<section class="user-section">
    <div class="user-container">
        
        <h2 class="user-section-title text-center mb-8">
            Tất cả sản phẩm
        </h2>
        
        <div class="product-layout">
            <!-- Sidebar Lọc Danh Mục -->
            <div class="filter-sidebar">
                <h3 class="filter-title">Danh mục sản phẩm</h3>
                <form id="filter-form" action="{{ url('/user/product') }}" method="GET">
                    <div class="flex flex-col gap-3">
                        @foreach($danhMucs as $dm)
                        <div class="filter-item">
                            <label class="filter-label">
                                <input type="checkbox" name="danh_muc_cb[]" value="{{ $dm->ma_danh_muc }}" class="filter-checkbox" {{ in_array($dm->ma_danh_muc, $selectedCategories) ? 'checked' : '' }}>
                                {{ $dm->ten_danh_muc }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="danh_muc" id="danh_muc_input" value="{{ request()->get('danh_muc') }}">
                    <button type="submit" class="mt-5 w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-xl transition shadow-md">
                        Lọc Sản Phẩm
                    </button>
                </form>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="product-main">
                <div class="product-grid">
                    @forelse($danhSachSanPham as $sp)
                        @include('components.product-card', ['sp' => $sp])
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-500 font-medium bg-gray-50 rounded-2xl border border-gray-100" style="grid-column: 1 / -1;">
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        const checkboxes = document.querySelectorAll('.filter-checkbox');
        const hiddenInput = document.getElementById('danh_muc_input');

        // Automatically submit form when checkbox changes (optional, but good UX)
        // Or we can rely on the submit button. Let's rely on the submit button for now as user might want to select multiple before filtering.

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect all checked values
            const checkedValues = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    checkedValues.push(cb.value);
                }
            });
            
            // Set hidden input value
            hiddenInput.value = checkedValues.join(',');
            
            // Disable all checkboxes before submit to prevent them from appearing in URL as danh_muc_cb[]=1&danh_muc_cb[]=2
            checkboxes.forEach(cb => {
                cb.disabled = true;
            });

            if (hiddenInput.value === '') {
                hiddenInput.disabled = true; // prevent ?danh_muc= in URL if empty
            }
            
            form.submit();
        });
    });
</script>
@endsection