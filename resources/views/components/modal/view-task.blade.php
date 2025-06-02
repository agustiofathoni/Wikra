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
                <label class="block text-gray-700 font-medium">Card Title</label>
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
