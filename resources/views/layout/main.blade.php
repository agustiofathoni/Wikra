<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wikra - Project Management</title>
    <!-- Move Sortable.js to head section -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="flex-col">
        @yield('content')
    </div>
</body>
</html>
