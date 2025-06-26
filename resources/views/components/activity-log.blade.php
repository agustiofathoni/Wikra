<!-- filepath: resources/views/components/activity-log.blade.php -->
<div id="activityLogSidebar" class="fixed top-0 right-0 h-full w-80 bg-white shadow-lg border-l border-gray-200 z-40 transition-transform duration-300 translate-x-0">
    <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">Activity Log</h2>
        <button onclick="hideActivityLog()" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
    </div>
    <ul class="divide-y divide-gray-100 max-h-[80vh] overflow-y-auto p-4">
        @forelse($activities as $activity)
            <li class="py-2 text-sm text-gray-700">
                <span class="font-semibold text-indigo-700">{{ $activity->user->name ?? 'Unknown' }}</span>
                melakukan <span class="font-semibold">{{ $activity->action }}</span>
                @if($activity->target)
                    pada <span class="italic">
                            @if($activity->target_type === \App\Models\BoardList::class)
                                this board
                            @elseif($activity->target_type === \App\Models\Task::class)
                                card
                            @elseif($activity->target_type === \App\Models\Board::class)
                                board
                            @else
                                {{ class_basename($activity->target_type) }}
                            @endif
                        </span>
                @endif
                <span class="text-xs text-gray-400 ml-2">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                @if($activity->description)
                    <div class="text-xs text-gray-500">{{ $activity->description }}</div>
                @endif
            </li>
        @empty
            <li class="py-2 text-gray-400">Belum ada aktivitas.</li>
        @endforelse
    </ul>
</div>
