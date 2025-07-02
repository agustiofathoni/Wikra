
export function initializeSortable() {
    const listsContainer = document.getElementById('listsContainer');
    if (!listsContainer) return;

    initializeListSorting(listsContainer);
    initializeTaskContainers();
    observeNewTaskContainers(listsContainer);
}

function initializeListSorting(listsContainer) {
    new Sortable(listsContainer, {
        animation: 150,
        draggable: '.list-item',
        handle: '.bg-white',
        filter: '#addListButton',
        ghostClass: 'opacity-50',
        onEnd: handleListReorder
    });
}

function handleListReorder(evt) {
    if (evt.oldIndex === evt.newIndex) return;

    const lists = Array.from(document.querySelectorAll('.list-item'))
        .map(el => parseInt(el.dataset.listId));

    updateListOrder(lists);
}

function updateListOrder(lists) {
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
        if (!data.success) throw new Error('Failed to reorder lists');
    })
    .catch(error => console.error('Error:', error));
}

function initializeTaskSortable(container) {
    new Sortable(container, {
        group: 'shared-tasks',
        animation: 150,
        draggable: '.task-item',
        ghostClass: 'opacity-50',
        onEnd: handleTaskReorder
    });
}

function handleTaskReorder(evt) {
    if (evt.from === evt.to && evt.oldIndex === evt.newIndex) return;

    const taskId = evt.item.dataset.taskId;
    const newListId = evt.to.closest('[data-list-id]').dataset.listId;
    const tasks = Array.from(evt.to.querySelectorAll('.task-item'))
        .map(el => parseInt(el.dataset.taskId));

    updateTaskOrder(taskId, newListId, tasks);
}

function updateTaskOrder(taskId, newListId, tasks) {
    fetch('/tasks/reorder', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ task_id: taskId, list_id: newListId, tasks })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) throw new Error('Failed to reorder tasks');
    })
    .catch(error => console.error('Error:', error));
}

export function initializeTaskContainers() {
    document.querySelectorAll('[data-list-id] .task-container')
        .forEach(initializeTaskSortable);
}

function observeNewTaskContainers(listsContainer) {
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.classList?.contains('task-container')) {
                    initializeTaskSortable(node);
                }
            });
        });
    });

    observer.observe(listsContainer, { childList: true, subtree: true });
}
