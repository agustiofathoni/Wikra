export function enableTaskEdit() {
    const title = document.getElementById('viewTaskTitle');
    const description = document.getElementById('viewTaskDescription');
    const saveButton = document.getElementById('saveTaskButton');

    title.removeAttribute('readonly');
    description.removeAttribute('readonly');
    title.focus();
    saveButton.classList.remove('hidden');
    saveButton.onclick = saveTaskChanges;
}

export function disableTaskEdit() {
    const title = document.getElementById('viewTaskTitle');
    const description = document.getElementById('viewTaskDescription');
    const saveButton = document.getElementById('saveTaskButton');

    title.setAttribute('readonly', true);
    description.setAttribute('readonly', true);
    saveButton.classList.add('hidden');
}

export function saveTaskChanges() {
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
        body: JSON.stringify({ title, description })
    })
    .then(response => response.json())
    .then(data => handleTaskUpdateSuccess(data, taskId, title, description, form))
    .catch(error => handleTaskUpdateError(error, form));
}

function handleTaskUpdateSuccess(data, taskId, title, description, form) {
    if (data.success) {
        const taskItem = document.querySelector(`[data-task-id="${taskId}"]`);
        taskItem.querySelector('p').textContent = title;

        document.getElementById('viewTaskTitle').value = title;
        document.getElementById('viewTaskDescription').value = description;

        taskItem.setAttribute('onclick',
            `openViewTaskModal(${taskId}, '${title}', '${description}')`
        );

        disableTaskEdit();
        showMessage(form, 'Changes saved successfully', 'text-green-500');
    }
}

function handleTaskUpdateError(error, form) {
    console.error('Error:', error);
    showMessage(form, 'Failed to save changes', 'text-red-500');
}

function showMessage(form, text, className) {
    const message = document.createElement('div');
    message.className = `${className} text-sm mb-2`;
    message.textContent = text;
    form.insertBefore(message, form.firstChild);
    setTimeout(() => message.remove(), 2000);
}
