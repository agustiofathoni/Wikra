<?php

namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Events\ListCreated;
use App\Events\ListDeleted;
use App\Events\ListReordered;
use App\Events\ListUpdated;
use App\Models\ActivityLog;
use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BoardListController extends Controller
{
    public function store(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $maxPosition = $board->lists()->max('position') ?? 0;

            $list = new BoardList();
            $list->name = $validated['name'];
            $list->board_id = $board->id;
            $list->position = $maxPosition + 1;
            $list->save();

            broadcast(new ListCreated($list))->toOthers();
             // Tambahkan activity log di sini
            $log = ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $board->id,
                'action' => 'create_list',
                'target_type' => BoardList::class,
                'target_id' => $list->id,
                'description' => 'List "' . $list->name . '" created in board "' . $board->title . '"',
                'created_at' => now(),
            ]);
            event(new \App\Events\ActivityLogged($log->id));
            DB::commit();
            return redirect()->route('boards.show', $board)->with('list_success', 'List created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create list');
        }
    }

    public function update(Request $request, BoardList $list)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $oldName = $list->name;
        $board = $list->board;

        $list->update($validated);

        // Broadcast event
        broadcast(new ListUpdated($list))->toOthers();

        // Tambahkan activity log
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $board->id,
            'action' => 'update_list',
            'target_type' => \App\Models\BoardList::class,
            'target_id' => $list->id,
            'description' => 'List diubah dari "' . $oldName . '" menjadi "' . $list->name . '" pada board "' . $board->title . '"',
            'created_at' => now(),
        ]);
        if($log){
            event(new ActivityLogged($log->id));
        }


        return redirect()->back()->with('list_success', 'List updated successfully');
    }

    public function destroy(BoardList $list)
    {
        $board = $list->board;
        $listId = $list->id;
        $boardId = $board->id;
        $listName = $list->name;


        // Broadcast event
        broadcast(new ListDeleted($listId, $boardId))->toOthers();
        // Tambahkan activity log
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $board->id,
            'action' => 'delete_list',
            'target_type' => \App\Models\BoardList::class,
            'target_id' => $listId,
            'description' => 'List "' . $listName . '" dihapus dari board "' . $board->title . '"',
            'created_at' => now(),
        ]);
        event(new ActivityLogged($log->id));
        $list->delete();
        return redirect()->route('boards.show', $board)->with('list_success', 'List deleted successfully');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'lists' => 'required|array',
            'lists.*' => 'numeric|exists:lists,id'
        ]);

        try {
            DB::beginTransaction();

            // Ambil data list pertama untuk dapatkan board_id
            $firstList = BoardList::find($validated['lists'][0]);
            $boardId = $firstList ? $firstList->board_id : null;

            // Ambil urutan posisi list SEBELUM update
            $oldOrder = BoardList::where('board_id', $boardId)
                ->orderBy('position')
                ->pluck('id')
                ->toArray();

            $newOrder = $validated['lists'];

            // Update posisi list
            foreach ($validated['lists'] as $position => $listId) {
                BoardList::where('id', $listId)
                        ->update(['position' => $position + 1]);
            }

            // Ambil data list terbaru untuk board ini, SEKALIGUS DENGAN TASKS-NYA
            $lists = BoardList::where('board_id', $boardId)
                ->orderBy('position')
                ->get()
                ->map(function($list) {
                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'position' => $list->position,
                        'board_id' => $list->board_id,
                        'tasks' => $list->tasks()->orderBy('position')->get(['id', 'title', 'description', 'list_id'])->toArray(),
                    ];
                })
                ->toArray();


            $listNames = BoardList::whereIn('id', array_merge($oldOrder, $newOrder))
                ->pluck('name', 'id')
                ->toArray();
            $oldNames = array_map(fn($id) => $listNames[$id] ?? $id, $oldOrder);
            $newNames = array_map(fn($id) => $listNames[$id] ?? $id, $newOrder);
            $log = null;
            if ($oldOrder !== $newOrder) {
                $log = ActivityLog::create([
                    'user_id' => Auth::id(),
                    'board_id' => $boardId,
                    'action' => 'reorder_list',
                    'target_type' => \App\Models\Board::class,
                    'target_id' => $boardId,
                    'description' => 'Urutan list diubah pada board "' . ($firstList->board->title ?? '-') . '". Sebelumnya: [' . implode(', ', $oldNames) . '] Menjadi: [' . implode(', ', $newNames) . ']',
                    'created_at' => now(),
                ]);
            }

            if ($boardId) {
                broadcast(new ListReordered($boardId, $lists))->toOthers();
            }

            DB::commit();
            if ($log) {
                event(new ActivityLogged($log->id));
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reorder failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

