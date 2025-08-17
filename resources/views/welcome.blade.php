<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OSIS SMK NEGERI 1 GARUT</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles (using Tailwind CDN for simplicity) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-gray-100 dark:bg-gray-900">
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center px-6">
            <h1 class="text-8xl md:text-9xl font-bold text-gray-700 dark:text-gray-300">404</h1>
            <p class="text-2xl md:text-3xl font-light text-gray-600 dark:text-gray-400 mt-4">
                Waduh, Halaman Nggak Ditemuin
            </p>
            <p class="mt-2 text-gray-500">
                Kayaknya lo nyasar deh. Coba balik lagi ke jalan yang bener.
            </p>

            <a href="{{ url('https://webex.smknegeri1garut.sch.id') }}"
               class="mt-8 inline-block px-6 py-3 text-sm font-semibold text-white bg-blue-500 rounded-md shadow-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                BALIKKK!
            </a>
        </div>
    </div>
</body>
</html>
