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
}

export function closeViewTaskModal() {
    document.getElementById('viewTaskModal').classList.add('hidden');
}
