<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Đồ án Bán Mỹ Phẩm')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded border border-gray-300 shadow-sm w-full max-w-md p-8">
        
        <div class="text-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Mỹ Phẩm AI</h1>
            <p class="text-sm text-gray-500">Đồ án tốt nghiệp</p>
        </div>

        @yield('content')
        
    </div>

</body>
</html>