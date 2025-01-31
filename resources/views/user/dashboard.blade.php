<!-- resources/views/user/dashboard.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    @include('partials.navbar') <!-- Pastikan Anda memiliki navbar jika diperlukan -->
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h1>
        <!-- Konten user lainnya di sini -->
    </div>
</body>
</html>
