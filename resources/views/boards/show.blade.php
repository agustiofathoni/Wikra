@extends('layout/main')

@section('meta')
    <meta name="board-id" content="{{ $board->id }}">
@endsection

@section('content')
@php
    $isOwner = auth()->id() === $board->user_id;
    $acceptedCollab = $board->collaborators->where('user_id', auth()->id())->where('status', 'accepted')->first();
    $myRole = $isOwner ? 'owner' : ($acceptedCollab ? $acceptedCollab->role : null);
@endphp

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

                @if($isOwner || $acceptedCollab)
                <button onclick="openInviteModal()" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-semibold px-3 py-2 rounded transition" title="Collaborators">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m13-7a4 4 0 11-8 0 4 4 0 018 0zM5 7a4 4 0 108 0 4 4 0 00-8 0z" />
                    </svg>
                    <span>Collaborators</span>
                </button>
                @endif
                <form onsubmit="event.preventDefault(); openConfirmLogoutModal();">
                    @csrf
                    <button type="submit" class="text-sm text-gray-700 hover:text-red-500 font-medium px-3 py-2 rounded transition">Logout</button>
                </form>
                 <button onclick="showActivityLog()" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">
                Activity Log
            </button>
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
                                @if ($isOwner || $myRole === 'edit')


                                <button onclick="openEditListModal({{ $list->id }}, '{{ $list->name }}')" class="text-indigo-500 hover:text-indigo-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>

                                <form action="{{ route('lists.destroy', $list) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-500 hover:text-red-700" onclick="openConfirmDeleteListModal({{ $list->id }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                            <div id="toast" class="fixed bottom-6 right-6 z-50 hidden bg-white border border-green-300 text-green-700 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 transition-all duration-300">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span id="toast-message"></span>
                                <button onclick="closeToast()" class="ml-2 text-gray-400 hover:text-gray-700">&times;</button>
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

                           @if($isOwner || $myRole === 'edit')
                            <button onclick="openAddTaskModal({{ $list->id }})" class="w-full text-left px-2 py-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                + Add a card
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

           @if($isOwner || $myRole === 'edit')
            <div class="w-72 flex-shrink-0" id="addListButton">
                <div class="bg-white border-2 border-dashed border-indigo-300 rounded-xl p-4 flex justify-center items-center">
                    <button onclick="openCreateListModal()" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        + Add a list
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

<!-- Confirm Delete List Modal -->
<div id="confirmDeleteListModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 shadow-lg max-w-sm w-full">
        <div class="flex flex-col items-center mb-2">
            <svg class="w-12 h-12 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <h2 class="text-lg font-bold mb-2 text-gray-800">Hapus List?</h2>
        </div>
        <p class="mb-4 text-gray-600 text-center">Apakah Anda yakin ingin menghapus list ini? Semua card di dalamnya juga akan terhapus.</p>
        <form id="deleteListForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeConfirmDeleteListModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Logout Modal -->
<div id="confirmLogoutModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 shadow-lg max-w-sm w-full">
        <div class="flex flex-col items-center mb-2">
            <svg class="w-12 h-12 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <h2 class="text-lg font-bold mb-2 text-gray-800">Logout?</h2>
        </div>
        <p class="mb-4 text-gray-600 text-center">Apakah Anda yakin ingin keluar dari aplikasi?</p>
        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
            @csrf
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeConfirmLogoutModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Logout</button>
            </div>
        </form>
    </div>
</div>
<!-- Confirm Delete Task Modal -->
<div id="confirmDeleteTaskModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 shadow-lg max-w-sm w-full">
        <div class="flex flex-col items-center mb-2">
            <svg class="w-12 h-12 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <h2 class="text-lg font-bold mb-2 text-gray-800">Hapus Card?</h2>
        </div>
        <p class="mb-4 text-gray-600 text-center">Apakah Anda yakin ingin menghapus card ini?</p>
        <form id="deleteTaskForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeConfirmDeleteTaskModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Hapus</button>
            </div>
        </form>
    </div>
</div>
<!-- Invite Modal -->
<div id="inviteModal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Collaborators</h2>
            <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        @if($isOwner)
            @if(session('invite_error'))
                <div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm">
                    {{ session('invite_error') }}
                </div>
            @endif
            @if(session('invite_success'))
                <div class="bg-green-100 text-green-700 px-3 py-2 rounded mb-2 text-sm">
                    {{ session('invite_success') }}
                </div>
            @endif
            <form action="{{ route('boards.invite', $board) }}" method="POST" class="mb-4 flex gap-2">
                @csrf
                <input type="email" name="email" placeholder="Invite by email" required class="border rounded p-2 flex-1">
                <select name="role" class="border rounded p-2">
                    <option value="view">View</option>
                    <option value="edit">Edit</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Invite</button>
            </form>
        @endif

        <h3 class="font-semibold mb-2">Collaborators:</h3>
        <ul>
          @foreach($board->collaborators as $collab)
            @if($collab->user)
                <li class="flex items-center gap-2 mb-1">
                    {{ $collab->user->name }} ({{ $collab->user->email }})
                    @if($collab->user_id == auth()->id())
                        <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">(You)</span>
                    @endif
                    -
                    <span class="text-xs px-2 py-1 rounded
                        @if($collab->status=='pending') bg-yellow-100 text-yellow-700
                        @elseif($collab->status=='accepted') bg-green-100 text-green-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($collab->status) }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                        {{ ucfirst($collab->role) }}
                    </span>
                    @if($isOwner)
                        <form action="{{ route('collaborators.updateRole', [$board, $collab]) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <select name="role" onchange="this.form.submit()" class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                <option value="view" @if($collab->role=='view') selected @endif>View</option>
                                <option value="edit" @if($collab->role=='edit') selected @endif>Edit</option>
                            </select>
                        </form>
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
@if(request('invite') == 1 && (session('invite_error') || session('invite_success')))
    window.addEventListener('DOMContentLoaded', function() {
        openInviteModal();
    });
@endif
window.myRole = "{{ $myRole }}";
window.isOwner = {{ $isOwner ? 'true' : 'false' }};

function showToast(message) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-message').textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}
function closeToast() {
    document.getElementById('toast').classList.add('hidden');
}
@if(session('list_success'))
    window.addEventListener('DOMContentLoaded', function() {
        showToast("{{ session('list_success') }}");
    });
@endif

function openConfirmDeleteListModal(listId) {
    const modal = document.getElementById('confirmDeleteListModal');
    const form = document.getElementById('deleteListForm');
    form.action = `/lists/${listId}`;
    modal.classList.remove('hidden');
}
function closeConfirmDeleteListModal() {
    document.getElementById('confirmDeleteListModal').classList.add('hidden');
}
function openConfirmLogoutModal() {
    document.getElementById('confirmLogoutModal').classList.remove('hidden');
}
function closeConfirmLogoutModal() {
    document.getElementById('confirmLogoutModal').classList.add('hidden');
}
function openConfirmDeleteTaskModal(taskId) {
    document.getElementById('viewTaskModal').classList.add('hidden');
    const modal = document.getElementById('confirmDeleteTaskModal');
    const form = document.getElementById('deleteTaskForm');
    form.action = `/tasks/${taskId}`;
    modal.classList.remove('hidden');
}
function closeConfirmDeleteTaskModal() {
    document.getElementById('confirmDeleteTaskModal').classList.add('hidden');
}
function showActivityLog() {
    document.getElementById('activityLogSidebar').style.transform = 'translateX(0)';
}
function hideActivityLog() {
    document.getElementById('activityLogSidebar').style.transform = 'translateX(100%)';
}
window.addEventListener('DOMContentLoaded', function() {
    hideActivityLog(); // Hide by default
});
</script>
    <x-modal.create-list :board="$board" />
    <x-modal.edit-list />
    <x-modal.add-task />
    <x-modal.view-task :board="$board" />
    <x-activity-log :activities="$activities" />
@endsection
