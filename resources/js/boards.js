import * as ModalHandlers from './modules/modal-handlers.js';
import * as TaskHandlers from './modules/task-handlers.js';
import { initializeSortable } from './modules/sortable-init.js';

const boardIdMeta = document.querySelector('meta[name="board-id"]');
const boardId = boardIdMeta ? boardIdMeta.content : null;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', () => {
     if (window.isOwner || window.myRole === 'edit') {
        initializeSortable();
    }

    // Modal event listeners
    document.querySelectorAll('#createListModal, #editListModal').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) {
                ModalHandlers.closeCreateListModal();
                ModalHandlers.closeEditListModal();
            }
        });
    });

    // Keyboard events
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            ModalHandlers.closeCreateListModal();
            ModalHandlers.closeEditListModal();
        }
    });

    window.Echo.channel(`board.${boardId}`)
    .listen('ListCreated', (e) => {
        console.log('ListCreated event received:', e);
        if (e.list) {
            addListToBoard(e.list);
        }
    })
    .listen('ListUpdated', (e) => {
        console.log('ListUpdated event received:', e);
        if (e.list) {
            updateListInBoard(e.list);
        }
    })
    .listen('ListDeleted', (e) => {
        console.log('ListDeleted event received:', e);
        if (e.list_id) {
            removeListFromBoard(e.list_id);
        }
    })
    .listen('ListReordered', (e) => {
        console.log('ListReordered event received:', e);
        if (Array.isArray(e.lists)) {
            updateListOrderInBoard(e.lists);
        }
    });
});
function addListToBoard(list) {
    const listsContainer = document.getElementById('listsContainer');
    if (!listsContainer) return;

    // Cek jika list sudah ada (hindari duplikat)
    if (document.querySelector(`[data-list-id="${list.id}"]`)) return;

    const div = document.createElement('div');
    div.className = 'w-72 flex-shrink-0 list-item';
    div.setAttribute('data-list-id', list.id);
    div.innerHTML = `
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold text-gray-800">${list.name}</h3>
                <div class="flex gap-2">
                    <button onclick="openEditListModal(${list.id}, '${list.name}')" class="text-indigo-500 hover:text-indigo-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <form action="/lists/${list.id}" method="POST" class="inline">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            <div class="space-y-2" data-list-id="${list.id}">
                <div class="task-container"></div>
                <button onclick="openAddTaskModal(${list.id})" class="w-full text-left px-2 py-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    + Add a card
                </button>
            </div>
        </div>
    `;
    // Sisipkan sebelum tombol add list
    const addListButton = document.getElementById('addListButton');
    listsContainer.insertBefore(div, addListButton);

    // Tambahkan task-task jika ada
    if (Array.isArray(list.tasks)) {
        const taskContainer = div.querySelector('.task-container');
        list.tasks.forEach(task => {
            const taskDiv = document.createElement('div');
            taskDiv.className = 'task-item bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg p-2 cursor-pointer mb-2';
            taskDiv.dataset.taskId = task.id;
            taskDiv.onclick = () => openViewTaskModal(task.id, task.title, task.description);
            taskDiv.innerHTML = `<p class="text-sm text-gray-800">${task.title}</p>`;
            taskContainer.appendChild(taskDiv);
        });
    }
}
function updateListInBoard(list) {
    // Temukan elemen list berdasarkan data-list-id
    const listDiv = document.querySelector(`.list-item[data-list-id="${list.id}"]`);
    if (listDiv) {
        // Update nama list
        const title = listDiv.querySelector('h3');
        if (title) title.textContent = list.name;

        // Update tombol edit agar nama baru terpakai
        const editBtn = listDiv.querySelector('button[onclick^="openEditListModal"]');
        if (editBtn) {
            editBtn.setAttribute('onclick', `openEditListModal(${list.id}, '${list.name}')`);
        }
    }
}
function removeListFromBoard(listId) {
    const listDiv = document.querySelector(`.list-item[data-list-id="${listId}"]`);
    if (listDiv) {
        listDiv.remove();
    }
}
function updateListOrderInBoard(lists) {
    const listsContainer = document.getElementById('listsContainer');
    if (!listsContainer) return;

    // Simpan tombol add list
    const addListButton = document.getElementById('addListButton');

    // Hapus semua list (kecuali tombol add list)
    listsContainer.querySelectorAll('.list-item').forEach(el => el.remove());

    // Tambahkan ulang list sesuai urutan baru
    lists.forEach(list => {
        addListToBoard(list);
    });

    // Pastikan tombol add list tetap di akhir
    if (addListButton) {
        listsContainer.appendChild(addListButton);
    }
}
// Make functions globally available
window.openCreateListModal = ModalHandlers.openCreateListModal;
window.closeCreateListModal = ModalHandlers.closeCreateListModal;
window.openEditListModal = ModalHandlers.openEditListModal;
window.closeEditListModal = ModalHandlers.closeEditListModal;
window.openAddTaskModal = ModalHandlers.openAddTaskModal;
window.closeTaskModal = ModalHandlers.closeTaskModal;
window.openViewTaskModal = ModalHandlers.openViewTaskModal;
window.closeViewTaskModal = ModalHandlers.closeViewTaskModal;
window.enableTaskEdit = TaskHandlers.enableTaskEdit;
window.disableTaskEdit = TaskHandlers.disableTaskEdit;
window.saveTaskChanges = TaskHandlers.saveTaskChanges;
