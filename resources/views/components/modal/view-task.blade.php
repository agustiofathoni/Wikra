@php
    $isOwner = auth()->id() === $board->user_id;
    $acceptedCollab = $board->collaborators->where('user_id', auth()->id())->where('status', 'accepted')->first();
    $myRole = $isOwner ? 'owner' : ($acceptedCollab ? $acceptedCollab->role : null);
@endphp

{{-- View Task Modal --}}
<div id="viewTaskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-bold">View Card</h2>
            <div class="flex space-x-2">
                @if ($isOwner || $myRole === 'edit')
                <button onclick="enableTaskEdit()" class="text-yellow-500 hover:text-yellow-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
               <button type="button" class="text-red-500 hover:text-red-700" onclick="openConfirmDeleteTaskModal(currentTaskId)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                @endif
                <button onclick="closeViewTaskModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-red-100">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
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
           <div class="flex justify-end items-center gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button"
                        id="cancelTaskButton"
                        onclick="cancelTaskEdit()"
                        class="hidden px-4 py-2 text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 text-sm font-medium">
                    Cancel
                </button>
                <button type="button"
                        id="saveTaskButton"
                        class="hidden px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 text-sm font-medium">
                    Save Changes
                </button>
            </div>
        </form>
        <div id="checklistSection" class="mb-4 mt-6 bg-gray-50 p-4 rounded-lg">
            <label class="block text-gray-700 font-medium mb-3">Checklist</label>
            <ul id="checklistItems" class="space-y-2 mb-4">
                {{-- Checklist items will be rendered here by JS --}}
            </ul>
            @if ($isOwner || $myRole === 'edit')


            <form id="addChecklistForm" class="flex gap-2 mt-4">
                <input type="text"
                    id="newChecklistText"
                    class="flex-1 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Add checklist item..."
                    required>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Add
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
<script>
    window.myRole = "{{ $myRole }}";
    window.isOwner = {{ $isOwner ? 'true' : 'false' }};
    window.currentTaskId = null;
</script>
