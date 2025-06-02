import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const boardIdMeta = document.querySelector('meta[name="board-id"]');
    if (!boardIdMeta) return;

    const boardId = boardIdMeta.content;
    console.log('Board ID:', boardId);
    // Initialize Pusher channel
  window.Echo.channel(`board.${boardId}`)
    .listen('TaskCreated', (e) => { // <--- tanpa titik di depan!
        console.log('TaskCreated event received:', e);
        if (e.task) {
            const existingTask = document.querySelector(`[data-task-id="${e.task.id}"]`);
            if (!existingTask) {
                addTaskToList(e.task);
            }
        }
    })
    .listen('TaskUpdated', (e) => {
        console.log('TaskUpdated event received:', e);
        if (e.task) {
            updateTaskInList(e.task);
        }
    })
    .listen('TaskDeleted', (e) => {
        console.log('TaskDeleted event received:', e);
        if (e.task_id) {
            removeTaskFromList(e.task_id);
        }
    })
    .listen('TaskReordered', (e) => {
        console.log('TaskReordered event received:', e);
        if (e.list_id && Array.isArray(e.tasks)) {
            updateTaskOrderInList(e.list_id, e.tasks);
        }
    });


});

function addTaskToList(task) {
    const listContainer = document.querySelector(`[data-list-id="${task.list_id}"] .task-container`);
    if (!listContainer) return;

    const taskElement = document.createElement('div');
    taskElement.className = 'task-item bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg p-2 cursor-pointer mb-2';
    taskElement.dataset.taskId = task.id;
    taskElement.onclick = () => openViewTaskModal(task.id, task.title, task.description);

    taskElement.innerHTML = `
        <p class="text-sm text-gray-800">${task.title}</p>
    `;

    listContainer.appendChild(taskElement);

    // Re-initialize Sortable if needed
    if (window.initializeTaskSortable) {
        window.initializeTaskSortable(listContainer);
    }
}
function updateTaskInList(task) {
    const taskElement = document.querySelector(`[data-task-id="${task.id}"]`);
    if (taskElement) {
        // Update title
        const titleEl = taskElement.querySelector('p');
        if (titleEl) titleEl.textContent = task.title;

        // Update onclick/view modal data
        taskElement.onclick = () => openViewTaskModal(task.id, task.title, task.description);
    }
}
function removeTaskFromList(taskId) {
    const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
    if (taskElement) {
        taskElement.remove();
    }
}
function updateTaskOrderInList(listId, tasks) {
    const allTaskIds = tasks.map(t => t.id);
    document.querySelectorAll('.task-item').forEach(el => {
        if (el.closest(`[data-list-id]`).dataset.listId !== String(listId) && allTaskIds.includes(Number(el.dataset.taskId))) {
            el.remove();
        }
    });
    const container = document.querySelector(`[data-list-id="${listId}"] .task-container`);
    if (!container) return;
    container.innerHTML = '';
    tasks.forEach(task => {
        addTaskToList(task);
    });




}
window.handleTaskSubmit = async function(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('/tasks', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Add task locally for immediate feedback
            addTaskToList(data.task);
            closeTaskModal();
            form.reset();
        } else {
            alert('Failed to add card');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to add card');
    }
}

// Export functions for use in other modules
window.addTaskToList = addTaskToList;
