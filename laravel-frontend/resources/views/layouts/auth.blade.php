<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ thống Mỹ Phẩm')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Ẩn icon con mắt mặc định của trình duyệt */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
    
    <div class="absolute inset-0 bg-cover bg-center blur-sm scale-105" 
         style="background-image: url('{{ asset('images/background.jpg') }}'); z-index: -2;">
    </div>
    
    <div class="absolute inset-0 bg-white/30" style="z-index: -1;"></div>


    <div class="z-10 w-full flex justify-center">
        @yield('content')
    </div>

</body>
</html>