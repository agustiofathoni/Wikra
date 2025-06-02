@extends('layout/main')

@section('content')
<!-- Navbar -->
<nav class="bg-white shadow-md border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16 items-center">
            <span class="text-2xl font-extrabold text-blue-600 flex items-center gap-2">
                📋 Wikra
            </span>
            <div class="flex items-center space-x-6">
                <span class="text-gray-700 font-medium">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md shadow transition">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-10 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-800">📋 My Boards</h1>
            <button onclick="openCreateBoardModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg text-sm font-semibold transition">+ Create Board</button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-400 text-green-800 px-5 py-4 rounded-lg shadow mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @forelse ($boards as $board)
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl border-t-4 border-blue-600 transition">
                    <h2 class="text-xl font-semibold text-gray-900 truncate">{{ $board->title }}</h2>
                    @if($board->description)
                        <p class="text-gray-600 mt-2 line-clamp-2">{{ $board->description }}</p>
                    @endif
                    <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                        <a href="{{ route('boards.show', $board) }}" class="text-blue-600 font-medium hover:underline">Open</a>
                        <div class="flex gap-4">
                            <button onclick="openEditModal({{ $board->id }}, '{{ $board->title }}', '{{ $board->description }}')" class="hover:text-yellow-600 font-medium">Edit</button>
                            <form method="POST" action="{{ route('boards.destroy', $board) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this board?')" class="hover:text-red-600 font-medium">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">You haven't created any boards yet.</p>
            @endforelse

        </div>
        <!-- Board Collaborator Section -->
@if($collaboratorBoards->count())
    <h2 class="text-2xl font-bold text-gray-800 mt-12 mb-4">🤝 Board Collaborator</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
        @foreach($collaboratorBoards as $board)
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500">
                <h2 class="text-xl font-semibold text-gray-900 truncate">{{ $board->title }}</h2>
                <p class="text-gray-600 text-sm mb-2">Owner: {{ $board->user->name }}</p>
                @if($board->description)
                    <p class="text-gray-600 mb-2 line-clamp-2">{{ $board->description }}</p>
                @endif
                <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                    <a href="{{ route('boards.show', $board) }}" class="text-green-600 font-medium hover:underline">Open</a>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Invitation Section -->
@if($pendingInvites->count())
    <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">📨 Board Invitation</h2>
    @foreach($pendingInvites as $collab)
        <div class="bg-yellow-50 border p-3 rounded mb-2 flex justify-between items-center">
            <div>
                <div class="font-semibold">{{ $collab->board->title }}</div>
                <div class="text-xs text-gray-500">From: {{ $collab->board->user->name }}</div>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('collaborators.approve', $collab) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-green-600 font-semibold">Setujui</button>
                </form>
                <form action="{{ route('collaborators.decline', $collab) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 font-semibold">Tolak</button>
                </form>
            </div>
        </div>
    @endforeach
@endif
    </div>
</div>

<!-- Create Modal -->
<div id="createBoardModal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Board</h2>
        <form method="POST" action="{{ route('boards.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Title</label>
                <input type="text" name="title" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-medium">Description</label>
                <textarea name="description" rows="3" class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300"></textarea>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeCreateBoardModal()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editBoardModal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Board</h2>
        <form id="editBoardForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Title</label>
                <input type="text" name="title" id="editBoardTitle" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-medium">Description</label>
                <textarea name="description" id="editBoardDesc" rows="3" class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300"></textarea>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeEditBoardModal()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Update</button>
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
function openEditModal(id, title, description) {
    document.getElementById('editBoardForm').action = `/boards/${id}`;
    document.getElementById('editBoardTitle').value = title;
    document.getElementById('editBoardDesc').value = description;
    document.getElementById('editBoardModal').classList.remove('hidden');
}
function closeEditBoardModal() {
    document.getElementById('editBoardModal').classList.add('hidden');
}
</script>
@endsection
