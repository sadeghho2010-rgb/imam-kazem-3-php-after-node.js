<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'سامانه جامع مدیریت حوزه علمیه') }}</title>
    
    <!-- Tailwind CSS CDN and Vazirmatn Font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Vazirmatn', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fcfbf9',
                            100: '#f7f5f0',
                            200: '#eee9dd',
                            500: '#b89d62',
                            600: '#9d8047',
                            700: '#7e6435',
                            800: '#5c4826',
                            900: '#3a2d18',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 min-h-screen antialiased flex flex-col">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                    ح
                </div>
                <div>
                    <h1 class="text-base font-bold text-stone-900">سامانه جامع مدیریت حوزه علمیه</h1>
                    <p class="text-xs text-stone-500">نگارش لاراول ۱۲ / دیتابیس MySQL 8</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-left">
                    <p class="text-sm font-semibold text-stone-800">حساب کاربری فعال</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                        متصل به دیتابیس سرور
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-stone-200 py-4 text-center text-xs text-stone-500">
        سامانه هوشمند مدیریت امور آموزشی، پژوهشی و مالی حوزه علمیه - توسعه‌یافته بر بستر Laravel 12 & MySQL
    </footer>

</body>
</html>
