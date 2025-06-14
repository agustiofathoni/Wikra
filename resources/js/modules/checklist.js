import '../bootstrap';

export function listenChecklistRealtime(taskId, loadChecklist) {
    if (window.EchoChecklistChannel) {
        window.Echo.leave(`task.${window.EchoChecklistChannel}`);
    }
    window.EchoChecklistChannel = taskId;
    window.Echo.channel(`task.${taskId}`)
        .listen('ChecklistCreated', (e) => {
            loadChecklist(taskId);
        })
        .listen('ChecklistUpdated', (e) => {
            loadChecklist(taskId);
        })
        .listen('ChecklistDeleted', (e) => {
            loadChecklist(taskId);
        });
}
