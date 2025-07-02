import { listenChecklistRealtime } from './checklist';
let currentTaskId = null;

function loadChecklist(taskId) {
    return fetch(`/tasks/${taskId}/checklists`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(items => {
            const ul = document.getElementById('checklistItems');
            ul.innerHTML = '';
            items.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('mb-2', 'bg-white', 'rounded-md', 'p-2', 'shadow-sm');

                // Cek role
                const canEdit = window.isOwner || window.myRole === 'edit';

                li.innerHTML = `
                    <label class="flex items-center justify-between gap-2 w-full">
                        <div class="flex items-center gap-2 flex-1">
                            <input type="checkbox" ${item.is_completed ? 'checked' : ''}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   ${canEdit ? `onchange="toggleChecklist(${item.id}, this.checked)"` : 'disabled'}>
                            <span id="checklist-text-${item.id}"
                                  class="flex-1 text-sm ${item.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}">${item.item_text}</span>
                            <input id="edit-checklist-input-${item.id}"
                                   class="hidden flex-1 border rounded p-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="${item.item_text}" />
                        </div>
                        <div class="flex items-center gap-1">
                            ${canEdit ? `
                           <!-- Edit Icon -->
                                <button type="button" onclick="showEditChecklist(${item.id})"
                                        class="text-yellow-400 hover:text-yellow-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <!-- Delete Icon -->
                                <button type="button" onclick="deleteChecklist(${item.id})"
                                        class="text-red-400 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                           <!-- Save Icon (Hidden by default) -->
                            <button type="button" id="save-edit-btn-${item.id}"
                                    onclick="saveEditChecklist(${item.id}, event)"
                                    class="hidden text-green-400 hover:text-green-500 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <!-- Cancel Icon (Hidden by default) -->
                            <button type="button" id="cancel-edit-btn-${item.id}"
                                    onclick="cancelEditChecklist(${item.id}, '${item.item_text.replace(/'/g, "\\'")}')"
                                    class="hidden text-red-400 hover:text-red-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            ` : ''}
                        </div>
                    </label>
                `;
                ul.appendChild(li);
            });
        })
        .catch(error => {
            console.error('Error loading checklists:', error);
        });
}


function toggleChecklist(id, checked) {
    fetch(`/checklists/${id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ is_completed: checked })
    }).then(() => loadChecklist(currentTaskId));
}

function deleteChecklist(id) {
    fetch(`/checklists/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        }
    }).then(() => loadChecklist(currentTaskId));
}


window.toggleChecklist = toggleChecklist;
window.deleteChecklist = deleteChecklist;


export function openCreateListModal() {
    document.getElementById('createListModal').classList.remove('hidden');
}

export function closeCreateListModal() {
    document.getElementById('createListModal').classList.add('hidden');
}

export function openEditListModal(listId, listName) {
    document.getElementById('editListForm').action = `/lists/${listId}`;
    document.getElementById('editListName').value = listName;
    document.getElementById('editListModal').classList.remove('hidden');
}

export function closeEditListModal() {
    document.getElementById('editListModal').classList.add('hidden');
}

export function openAddTaskModal(listId) {
    document.getElementById('taskModalTitle').textContent = 'Add Card';
    document.getElementById('taskForm').action = '/tasks';
    document.getElementById('taskForm').method = 'POST';
    document.getElementById('taskListId').value = listId;
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskModal').classList.remove('hidden');
}

export function closeTaskModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

window.showEditChecklist = function(id) {
    document.getElementById(`checklist-text-${id}`).classList.add('hidden');
    document.getElementById(`edit-checklist-input-${id}`).classList.remove('hidden');
    document.getElementById(`save-edit-btn-${id}`).classList.remove('hidden');
    document.getElementById(`cancel-edit-btn-${id}`).classList.remove('hidden');
};

window.cancelEditChecklist = function(id, originalText) {
    document.getElementById(`edit-checklist-input-${id}`).classList.add('hidden');
    document.getElementById(`save-edit-btn-${id}`).classList.add('hidden');
    document.getElementById(`cancel-edit-btn-${id}`).classList.add('hidden');
    document.getElementById(`checklist-text-${id}`).classList.remove('hidden');
    document.getElementById(`edit-checklist-input-${id}`).value = originalText;
};

window.saveEditChecklist = function(id, event) {
    event.preventDefault();
    event.stopPropagation();

    const input = document.getElementById(`edit-checklist-input-${id}`);
    const newText = input.value;

    fetch(`/checklists/${id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ item_text: newText })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(() => {
        document.getElementById(`edit-checklist-input-${id}`).classList.add('hidden');
        document.getElementById(`save-edit-btn-${id}`).classList.add('hidden');
        document.getElementById(`cancel-edit-btn-${id}`).classList.add('hidden');
        const textSpan = document.getElementById(`checklist-text-${id}`);
        textSpan.textContent = newText;
        textSpan.classList.remove('hidden');
        loadChecklist(currentTaskId);
    })
    .catch(error => {
        console.error('Error updating checklist:', error);
    });
};
export function openViewTaskModal(taskId, title, description) {
    window.currentTaskId = taskId;
    const modal = document.getElementById('viewTaskModal');
    const form = document.getElementById('viewTaskForm');
    const deleteForm = document.getElementById('deleteTaskForm');


    document.getElementById('viewTaskTitle').value = title;
    document.getElementById('viewTaskDescription').value = description;
   if (deleteForm) {
        deleteForm.action = `/tasks/${taskId}`;
    }
    if (form) {
        form.dataset.taskId = taskId;
    }
    disableTaskEdit();
    modal.classList.remove('hidden');

    currentTaskId = taskId;
    loadChecklist(taskId);
    listenChecklistRealtime(taskId, loadChecklist);

     // Event listener hanya dipasang saat modal dibuka
    const addChecklistForm = document.getElementById('addChecklistForm');
    if (addChecklistForm && !addChecklistForm.dataset.listener) {
        addChecklistForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const text = document.getElementById('newChecklistText').value;
            fetch(`/tasks/${currentTaskId}/checklists`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ item_text: text })
            })
            .then(res => res.json())
            .then(() => {
                document.getElementById('newChecklistText').value = '';
                loadChecklist(currentTaskId);
            });
        });
        addChecklistForm.dataset.listener = "true";
    }

    modal.addEventListener('click', function(event) {
        // Jika yang diklik adalah modal backdrop (bukan konten modal)
        if (event.target === modal) {
            closeViewTaskModal();
        }
    });

    currentTaskId = taskId;
    loadChecklist(taskId);
    listenChecklistRealtime(taskId, loadChecklist);
}

export function closeViewTaskModal() {
    document.getElementById('viewTaskModal').classList.add('hidden');
}

