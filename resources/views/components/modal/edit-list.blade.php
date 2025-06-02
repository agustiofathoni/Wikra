<div id="editListModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit List</h2>
        <form id="editListForm" method="POST">
            @csrf
            @method('PUT')
            <input
                type="text"
                name="name"
                id="editListName"
                placeholder="Enter list name"
                class="w-full border rounded p-2 mb-4"
                required
            >
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditListModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Update List
                </button>
            </div>
        </form>
    </div>
</div>
