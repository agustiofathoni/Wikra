@extends('layout/main')

@section('meta')
    <meta name="board-id" content="{{ $board->id }}">
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-indigo-500 hover:text-indigo-700" title="Back to Dashboard">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">{{ $board->title }}</h1>
            </div>
            <div class="flex items-center gap-6">
                <span class="text-sm text-gray-600">Created by <span class="font-semibold text-indigo-600">{{ $board->user->name }}</span></span>
                @if(auth()->id() === $board->user_id)
                <button onclick="openInviteModal()" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-semibold px-3 py-2 rounded transition" title="Invite Collaborator">
                    <!-- Heroicons: User Group -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m13-7a4 4 0 11-8 0 4 4 0 018 0zM5 7a4 4 0 108 0 4 4 0 00-8 0z" />
                    </svg>
                    <span>Collaborators</span>
                </button>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-gray-700 hover:text-red-500 font-medium px-3 py-2 rounded transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Board Description -->
    <div class="p-6">
    <div class="max-w-7xl mx-auto space-y-4">
        @if($board->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi Board</h2>
                <p class="text-gray-600 leading-relaxed">{{ $board->description }}</p>
            </div>
        @endif

        <!-- Lists Container -->
        <div class="flex gap-4 overflow-x-auto pb-4" id="listsContainer">
            @foreach($board->lists()->orderBy('position')->get() as $list)
                <div class="w-72 flex-shrink-0 list-item" data-list-id="{{ $list->id }}">
                    <div class="bg-white rounded-xl shadow-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-gray-800">{{ $list->name }}</h3>
                            <div class="flex gap-2">
                                <button onclick="openEditListModal({{ $list->id }}, '{{ $list->name }}')" class="text-indigo-500 hover:text-indigo-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <form action="{{ route('lists.destroy', $list) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="space-y-2" data-list-id="{{ $list->id }}">
                            <div class="task-container">
                                @foreach($list->tasks()->orderBy('position')->get() as $task)
                                    <div class="task-item bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg p-2 cursor-pointer mb-2"
                                        data-task-id="{{ $task->id }}"
                                        onclick="openViewTaskModal({{ $task->id }}, '{{ $task->title }}', '{{ addslashes($task->description) }}')">
                                        <p class="text-sm text-gray-800">{{ $task->title }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <button onclick="openAddTaskModal({{ $list->id }})" class="w-full text-left px-2 py-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                + Add a card
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="w-72 flex-shrink-0" id="addListButton">
                <div class="bg-white border-2 border-dashed border-indigo-300 rounded-xl p-4 flex justify-center items-center">
                    <button onclick="openCreateListModal()" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        + Add a list
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invite Modal -->
<div id="inviteModal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Invite Collaborator</h2>
            <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        @if(session('error'))
            <div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-3 py-2 rounded mb-2 text-sm">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('boards.invite', $board) }}" method="POST" class="mb-4 flex gap-2">
            @csrf
            <input type="email" name="email" placeholder="Invite by email" required class="border rounded p-2 flex-1">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Invite</button>
        </form>
        <h3 class="font-semibold mb-2">Collaborators:</h3>
        <ul>
           @foreach($board->collaborators as $collab)
                @if($collab->user)
                    <li class="flex items-center gap-2 mb-1">
                        {{ $collab->user->name }} ({{ $collab->user->email }}) -
                        <span class="text-xs px-2 py-1 rounded
                            @if($collab->status=='pending') bg-yellow-100 text-yellow-700
                            @elseif($collab->status=='accepted') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($collab->status) }}
                        </span>
                        @if(auth()->id() === $board->user_id)
                            <form action="{{ route('collaborators.remove', [$board, $collab]) }}" method="POST" onsubmit="return confirm('Remove this collaborator?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 text-xs">Remove</button>
                            </form>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

<script>
function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
}
function closeInviteModal() {
    document.getElementById('inviteModal').classList.add('hidden');
}
@if(request('invite') == 1 && (session('error') || session('success')))
    window.addEventListener('DOMContentLoaded', function() {
        openInviteModal();
    });
@endif
</script>
    <x-modal.create-list :board="$board" />
    <x-modal.edit-list />
    <x-modal.add-task />
    <x-modal.view-task />
@endsection
