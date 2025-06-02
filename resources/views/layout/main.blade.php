<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wikra - Project Management</title>
    @yield('meta')
    <!-- Move Sortable.js to head section -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="flex-col">
        @yield('content')
    </div>
    @vite(['resources/js/app.js', 'resources/css/app.css', 'resources/js/boards.js', 'resources/js/tasks.js'])
    @stack('scripts')
</body>

</html>
