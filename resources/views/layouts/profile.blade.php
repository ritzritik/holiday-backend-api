<!-- resources/views/layouts/admin/master.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 shadow p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-xl font-bold">Admin Panel</h1>
            <div>
                <a href="{{ url('/admin/profile') }}" class="text-white hover:text-blue-200">Profile</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto py-8">
        @yield('content')
    </main>
</body>
</html>
