<div id="taskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4" id="taskModalTitle">Add Card</h2>
        <form id="taskForm" method="POST" onsubmit="handleTaskSubmit(event)">
            @csrf
            <input type="hidden" name="list_id" id="taskListId">
            <div class="mb-4">
                <input type="text" name="title" id="taskTitle" placeholder="Enter card title"
                    class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <textarea name="description" id="taskDescription" placeholder="Enter card description (optional)"
                    class="w-full border rounded p-2 h-24"></textarea>
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
