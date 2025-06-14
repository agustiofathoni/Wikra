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
                li.innerHTML = `
                    <label class="flex items-center justify-between gap-2 w-full">
                        <div class="flex items-center gap-2 flex-1">
                            <input type="checkbox" ${item.is_completed ? 'checked' : ''}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   onchange="toggleChecklist(${item.id}, this.checked)">
                            <span id="checklist-text-${item.id}"
                                  class="flex-1 text-sm ${item.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}">${item.item_text}</span>
                            <input id="edit-checklist-input-${item.id}"
                                   class="hidden flex-1 border rounded p-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="${item.item_text}" />
                        </div>
                        <div class="flex items-center gap-1">
                            <!-- Edit Icon -->
                            <button type="button" onclick="showEditChecklist(${item.id})"
                                    class="text-gray-400 hover:text-blue-500 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            <!-- Delete Icon -->
                            <button type="button" onclick="deleteChecklist(${item.id})"
                                    class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m6.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                            <!-- Save Icon (Hidden by default) -->
                            <button type="button" id="save-edit-btn-${item.id}"
                                    onclick="saveEditChecklist(${item.id}, event)"
                                    class="hidden text-gray-400 hover:text-green-500 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <!-- Cancel Icon (Hidden by default) -->
                            <button type="button" id="cancel-edit-btn-${item.id}"
                                    onclick="cancelEditChecklist(${item.id}, '${item.item_text.replace(/'/g, "\\'")}')"
                                    class="hidden text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
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
    const modal = document.getElementById('viewTaskModal');
    const form = document.getElementById('viewTaskForm');
    const deleteForm = document.getElementById('deleteTaskForm');



    document.getElementById('viewTaskTitle').value = title;
    document.getElementById('viewTaskDescription').value = description;
    deleteForm.action = `/tasks/${taskId}`;
    form.dataset.taskId = taskId;
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

