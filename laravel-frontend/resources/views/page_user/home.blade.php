@extends('layouts.user')

@section('title', 'Trang chủ')

@section('content')

<section class="bg-gray-300 py-12 px-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-400 h-48 rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition cursor-pointer">
                <span class="text-gray-700 font-bold text-lg">Quảng cáo 1</span>
            </div>
            <div class="bg-gray-400 h-48 rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition cursor-pointer">
                <span class="text-gray-700 font-bold text-lg">Quảng cáo 2</span>
            </div>
            <div class="bg-gray-400 h-48 rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition cursor-pointer">
                <span class="text-gray-700 font-bold text-lg">Quảng cáo 3</span>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#fcfdf2] py-16 px-8">
    <div class="max-w-7xl mx-auto">
        
        <h2 class="text-2xl font-black text-gray-800 text-center mb-10 uppercase tracking-wide">
            Sản phẩm nổi bật
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            
            <div class="bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative">
                <span class="text-gray-600 font-bold text-xl z-10">Sản phẩm 1</span>
            </div>

            <div class="bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative">
                <span class="text-gray-600 font-bold text-xl z-10">Sản phẩm 2</span>
            </div>

            <div class="bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative">
                <span class="text-gray-600 font-bold text-xl z-10">Sản phẩm 3</span>
            </div>

            <div class="bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative">
                <span class="text-gray-600 font-bold text-xl z-10">Sản phẩm 4</span>
            </div>

            <div class="bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative">
                <span class="text-gray-600 font-bold text-xl z-10">Sản phẩm 5</span>
            </div>

            <div class="bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative">
                <span class="text-gray-600 font-bold text-xl z-10">Sản phẩm 6</span>
            </div>

        </div>

    </div>
</section>

@endsection