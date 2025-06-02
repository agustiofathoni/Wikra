<?php

namespace App\Http\Controllers;

use App\Events\ListCreated;
use App\Events\ListDeleted;
use App\Events\ListReordered;
use App\Events\ListUpdated;
use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            DB::commit();
            return redirect()->route('boards.show', $board)->with('success', 'List created successfully');
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

        $list->update($validated);

        // Broadcast event
        broadcast(new ListUpdated($list))->toOthers();

        return redirect()->back()->with('success', 'List updated successfully');
    }

    public function destroy(BoardList $list)
    {
        $board = $list->board;
        $listId = $list->id;
        $boardId = $board->id;
        $list->delete();

        // Broadcast event
        broadcast(new ListDeleted($listId, $boardId))->toOthers();

        return redirect()->route('boards.show', $board)->with('success', 'List deleted successfully');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'lists' => 'required|array',
            'lists.*' => 'numeric|exists:lists,id'
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['lists'] as $position => $listId) {
                BoardList::where('id', $listId)
                        ->update(['position' => $position + 1]);
            }

            // Ambil data list terbaru untuk board ini, SEKALIGUS DENGAN TASKS-NYA
            $firstList = BoardList::find($validated['lists'][0]);
            $boardId = $firstList ? $firstList->board_id : null;

            // --- GANTI BAGIAN INI ---
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
            // --- SAMPAI SINI ---

            // Broadcast event
            if ($boardId) {
                broadcast(new ListReordered($boardId, $lists))->toOthers();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reorder failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

