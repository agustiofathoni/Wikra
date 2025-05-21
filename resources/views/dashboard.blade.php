@extends('layout/main')

@section('container')
<div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <div class="flex items-center justify-between px-6 py-3 border-b bg-white">
        <div class="flex items-center space-x-3">
            <div class="text-xl font-bold">Wikra</div>
            <div class="relative w-[500px]">
                <input type="text" placeholder="Search boards..." class="px-4 py-2 border rounded-md w-72" />
                <span class="absolute right-3 top-2.5 text-gray-400">🔍</span>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="openCreateBoardModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Board
            </button>
            <form method="POST" action="/logout" class="flex items-center">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-gray-800">
                    <span class="text-xl">👤</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main content -->
    <main class="p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Your Boards -->
            <h2 class="text-xl font-semibold mb-4">Your Boards</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- @foreach($boards as $board)
                <a href="/boards/{{ $board->id }}" class="block">
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="h-32 bg-blue-100 rounded-t-lg"></div>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900">{{ $board->title }}</h3>
                            @if($board->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $board->description }}</p>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach --}}

                <!-- Create Board Card -->
                <button onclick="openCreateBoardModal()" class="block h-full">
                    <div class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 h-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                        <div class="text-center">
                            <span class="block text-2xl mb-1">+</span>
                            <span class="text-gray-600">Create New Board</span>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </main>

    <!-- Create Board Modal -->
    <div id="createBoardModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Create New Board</h3>
            <form action="/boards" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Board Title</label>
                        <input type="text" name="title" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                        <textarea name="description" rows="3" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateBoardModal()" 
                            class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            Create Board
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateBoardModal() {
            document.getElementById('createBoardModal').classList.remove('hidden');
        }

        function closeCreateBoardModal() {
            document.getElementById('createBoardModal').classList.add('hidden');
        }
    </script>
</div>
@endsection
