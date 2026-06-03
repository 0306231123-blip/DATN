@extends('layouts.user') 
{{-- Lưu ý: @extends có thể khác tùy thuộc vào tên layout của bạn --}}

@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="flex flex-col items-center">
                <div class="w-full bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-inner mb-6 overflow-hidden">
                    {{-- Chỗ này tạm để chữ, sau này mình gắn link ảnh thật vào đây --}}
                    <span class="text-gray-500 font-bold">Hình ảnh: {{ $sanPham->ten_san_pham }}</span>
                </div>
                <div class="flex space-x-4 w-full">
                    <div class="flex-1 bg-white p-4 rounded-xl text-center shadow-sm font-bold text-pink-600 text-xl">
                        {{-- Format giá tiền chuẩn VNĐ --}}
                        {{ number_format($sanPham->gia, 0, ',', '.') }} VNĐ
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-xl text-center shadow-sm font-bold text-gray-400 line-through flex items-center justify-center">
                        Giá KM (nếu có)
                    </div>
                </div>
                <button class="mt-6 w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 rounded-xl shadow-md transition uppercase tracking-wider">
                    Thêm vào giỏ hàng
                </button>
            </div>

            <div class="flex flex-col">
                <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-2xl text-gray-800 mb-6">
                    {{ $sanPham->ten_san_pham }}
                </div>
                
                <div class="flex gap-6">
                    <div class="flex-1 bg-white p-6 rounded-2xl shadow-sm min-h-[300px]">
                        <h3 class="font-bold text-gray-800 mb-2">Mô tả sản phẩm:</h3>
                        <p class="text-gray-600 whitespace-pre-line mb-4">{{ $sanPham->mo_ta }}</p>
                        
                        <h3 class="font-bold text-gray-800 mb-2 mt-4">Thành phần chính:</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $sanPham->thanh_phan }}</p>
                    </div>
                    
                    <div class="flex flex-col space-y-3 w-1/3">
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">
                            Hãng: {{ $sanPham->thuong_hieu }}
                        </span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">
                            Xuất xứ: {{ $sanPham->xuat_xu }}
                        </span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">
                            Dành cho: {{ $sanPham->loai_da_phu_hop }}
                        </span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-400 mt-4">
                            Còn lại: {{ $sanPham->so_luong_ton }} sp
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection