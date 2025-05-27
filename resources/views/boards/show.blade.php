@extends('layout/main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-indigo-500 hover:text-indigo-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">{{ $board->title }}</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Created by {{ $board->user->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-gray-700 hover:text-red-500 font-medium">Logout</button>
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

<!-- Create List Modal -->
<div id="createListModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Create New List</h2>
        <form action="{{ route('lists.store', $board) }}" method="POST">
            @csrf
            <input
                type="text"
                name="name"
                placeholder="Enter list name"
                class="w-full border rounded p-2 mb-4"
                required
            >
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeCreateListModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create List
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit List Modal -->
<div id="editListModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit List</h2>
        <form id="editListForm" method="POST">
            @csrf
            @method('PUT')
            <input
                type="text"
                name="name"
                id="editListName"
                placeholder="Enter list name"
                class="w-full border rounded p-2 mb-4"
                required
            >
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditListModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Update List
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Task Modal -->
<div id="taskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4" id="taskModalTitle">Add Card</h2>
        <form id="taskForm" method="POST">
            @csrf
            <input type="hidden" name="list_id" id="taskListId">
            <div class="mb-4">
                <input
                    type="text"
                    name="title"
                    id="taskTitle"
                    placeholder="Enter card title"
                    class="w-full border rounded p-2"
                    required
                >
            </div>
            <div class="mb-4">
                <textarea
                    name="description"
                    id="taskDescription"
                    placeholder="Enter card description (optional)"
                    class="w-full border rounded p-2 h-24"
                ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeTaskModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View/Edit Task Modal -->
<div id="viewTaskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-bold">View Card</h2>
            <div class="flex space-x-2">
                <button onclick="enableTaskEdit()" class="text-yellow-500 hover:text-yellow-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
                <form id="deleteTaskForm" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this card?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <form id="viewTaskForm">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium"> Card Title</label>
                <input type="text" id="viewTaskTitle" class="w-full border rounded p-2" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Description</label>
                <textarea id="viewTaskDescription" class="w-full border rounded p-2 h-24" readonly></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeViewTaskModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Close
                </button>
                <button type="button" id="saveTaskButton" class="hidden px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal Functions
    function openCreateListModal() {
        document.getElementById('createListModal').classList.remove('hidden');
    }

    function closeCreateListModal() {
        document.getElementById('createListModal').classList.add('hidden');
    }

    function openEditListModal(listId, listName) {
        document.getElementById('editListForm').action = `/lists/${listId}`;
        document.getElementById('editListName').value = listName;
        document.getElementById('editListModal').classList.remove('hidden');
    }

    function closeEditListModal() {
        document.getElementById('editListModal').classList.add('hidden');
    }

    // Modal Event Listeners
    document.querySelectorAll('#createListModal, #editListModal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateListModal();
                closeEditListModal();
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateListModal();
            closeEditListModal();
        }
    });

    // Initialize Sortable
// Update the task sorting initialization
// Replace all Sortable initialization code with this single implementation
document.addEventListener('DOMContentLoaded', function() {
    const listsContainer = document.getElementById('listsContainer');
    if (listsContainer) {
        // Initialize list sorting
        new Sortable(listsContainer, {
            animation: 150,
            draggable: '.list-item',
            handle: '.bg-white',
            filter: '#addListButton',
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                if (evt.oldIndex === evt.newIndex) return;

                const lists = Array.from(document.querySelectorAll('.list-item'))
                    .map(el => parseInt(el.dataset.listId));

                fetch('/lists/reorder', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ lists })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error('Failed to reorder lists');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });

        // Initialize task sorting for each list
        function initializeTaskSortable(container) {
            new Sortable(container, {
                group: 'shared-tasks',
                animation: 150,
                draggable: '.task-item',
                ghostClass: 'opacity-50',
                onEnd: function(evt) {
                    if (evt.from === evt.to && evt.oldIndex === evt.newIndex) return;

                    const taskId = evt.item.dataset.taskId;
                    const newListId = evt.to.closest('[data-list-id]').dataset.listId;
                    const tasks = Array.from(evt.to.querySelectorAll('.task-item'))
                        .map(el => parseInt(el.dataset.taskId));

                    fetch('/tasks/reorder', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            task_id: taskId,
                            list_id: newListId,
                            tasks: tasks
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error('Failed to reorder tasks');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        }

        // Initialize existing task containers
        document.querySelectorAll('[data-list-id] .task-container').forEach(initializeTaskSortable);

        // Create a MutationObserver to watch for new task containers
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.classList && node.classList.contains('task-container')) {
                        initializeTaskSortable(node);
                    }
                });
            });
        });

        // Start observing the lists container
        observer.observe(listsContainer, { childList: true, subtree: true });
    }
});

// Remove the duplicate Sortable initialization code that was at the bottom
function openAddTaskModal(listId) {
    document.getElementById('taskModalTitle').textContent = 'Add Card';
    document.getElementById('taskForm').action = '/tasks';
    document.getElementById('taskForm').method = 'POST';
    document.getElementById('taskListId').value = listId;
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskModal').classList.remove('hidden');
}

function closeTaskModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

// Add to your DOMContentLoaded event listener
// document.querySelectorAll('.task-item').forEach(taskEl => {
//     new Sortable(taskEl.parentElement, {
//         group: 'tasks',
//         animation: 150,
//         draggable: '.task-item',
//         ghostClass: 'opacity-50',
//         onEnd: function(evt) {
//             const taskId = evt.item.dataset.taskId;
//             const newListId = evt.to.closest('[data-list-id]').dataset.listId;
//             const tasks = Array.from(evt.to.querySelectorAll('[data-task-id]'))
//                 .map(el => parseInt(el.dataset.taskId));

//             fetch('/tasks/reorder', {
//                 method: 'PUT',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
//                     'Accept': 'application/json'
//                 },
//                 body: JSON.stringify({
//                     task_id: taskId,
//                     list_id: newListId,
//                     tasks: tasks
//                 })
//             })
//             .catch(error => console.error('Error:', error));
//         }
//     });
// });

// Add these functions to your existing script section
function openViewTaskModal(taskId, title, description) {
    const modal = document.getElementById('viewTaskModal');
    const form = document.getElementById('viewTaskForm');
    const deleteForm = document.getElementById('deleteTaskForm');

    // Set form values
    document.getElementById('viewTaskTitle').value = title;
    document.getElementById('viewTaskDescription').value = description;

    // Set up delete form action
    deleteForm.action = `/tasks/${taskId}`;

    // Store task ID for update
    form.dataset.taskId = taskId;

    // Reset to view mode
    disableTaskEdit();

    modal.classList.remove('hidden');
}

function closeViewTaskModal() {
    document.getElementById('viewTaskModal').classList.add('hidden');
}

function enableTaskEdit() {
    const title = document.getElementById('viewTaskTitle');
    const description = document.getElementById('viewTaskDescription');
    const saveButton = document.getElementById('saveTaskButton');

    title.removeAttribute('readonly');
    description.removeAttribute('readonly');
    title.focus();
    saveButton.classList.remove('hidden');

    // Add save handler
    saveButton.onclick = saveTaskChanges;
}

function disableTaskEdit() {
    const title = document.getElementById('viewTaskTitle');
    const description = document.getElementById('viewTaskDescription');
    const saveButton = document.getElementById('saveTaskButton');

    title.setAttribute('readonly', true);
    description.setAttribute('readonly', true);
    saveButton.classList.add('hidden');
}

function saveTaskChanges() {
    const form = document.getElementById('viewTaskForm');
    const taskId = form.dataset.taskId;
    const title = document.getElementById('viewTaskTitle').value;
    const description = document.getElementById('viewTaskDescription').value;

    fetch(`/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            title: title,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the card title in the list
            const taskItem = document.querySelector(`[data-task-id="${taskId}"]`);

            // Update title in list view
            taskItem.querySelector('p').textContent = title;

            // Update title and description in modal
            document.getElementById('viewTaskTitle').value = title;
            document.getElementById('viewTaskDescription').value = description;

            // Store updated values in data attributes for next opening
            taskItem.setAttribute('onclick',
                `openViewTaskModal(${taskId}, '${title}', '${description}')`
            );

            // Return to view mode
            disableTaskEdit();

            // Optional: Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'text-green-500 text-sm mb-2';
            successMessage.textContent = 'Changes saved successfully';
            form.insertBefore(successMessage, form.firstChild);

            // Remove success message after 2 seconds
            setTimeout(() => successMessage.remove(), 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message to user
        const errorMessage = document.createElement('div');
        errorMessage.className = 'text-red-500 text-sm mb-2';
        errorMessage.textContent = 'Failed to save changes';
        form.insertBefore(errorMessage, form.firstChild);

        // Remove error message after 2 seconds
        setTimeout(() => errorMessage.remove(), 2000);
    });
}
</script>
@endsection
