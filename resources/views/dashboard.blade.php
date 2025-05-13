@extends('layout/main')

@section('container')
<div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">Wikra</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="/logout" method="POST" class="flex items-center">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">My Boards</h2>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Create Board
                </button>
            </div>

            <!-- Boards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Sample Board Card -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition">
                    <h3 class="font-medium text-gray-900">Project Board</h3>
                    <p class="text-sm text-gray-500 mt-1">Created on May 13, 2025</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
