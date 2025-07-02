<?php

namespace App\Services;

use App\Events\ListCreated;
use App\Events\ListDeleted;
use App\Events\ListReordered;
use App\Events\ListUpdated;
use App\Models\Board;
use App\Models\BoardList;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;

class BoardListService
{
    use LogsActivity;

    private $board;

    protected function getBoardId(): int
    {
        return $this->board->id;
    }

    public function createList(Board $board, array $data): BoardList
    {
        $this->board = $board;

        return DB::transaction(function () use ($data) {
            $maxPosition = $this->board->lists()->max('position') ?? 0;

            $list = new BoardList();
            $list->name = $data['name'];
            $list->board_id = $this->board->id;
            $list->position = $maxPosition + 1;
            $list->save();

            broadcast(new ListCreated($list))->toOthers();

            $this->logActivity('create_list', 'List "' . $list->name . '" created in board "' . $this->board->title . '"', $list);

            return $list;
        });
    }

    public function updateList(BoardList $list, array $data): BoardList
    {
        $this->board = $list->board;
        $oldName = $list->name;

        $list->update($data);

        broadcast(new ListUpdated($list))->toOthers();

        $this->logActivity('update_list', 'List diubah dari "' . $oldName . '" menjadi "' . $list->name . '" pada board "' . $this->board->title . '"', $list);

        return $list;
    }

    public function deleteList(BoardList $list)
    {
        $this->board = $list->board;
        $listId = $list->id;
        $boardId = $this->board->id;
        $listName = $list->name;

        broadcast(new ListDeleted($listId, $boardId))->toOthers();

        $this->logActivity('delete_list', 'List "' . $listName . '" dihapus dari board "' . $this->board->title . '"', $list);

        $list->delete();
    }

    public function reorderLists(array $listIds)
    {
        DB::transaction(function () use ($listIds) {
            $firstList = BoardList::find($listIds[0]);
            $this->board = $firstList->board;

            $oldOrder = BoardList::where('board_id', $this->board->id)
                ->orderBy('position')
                ->pluck('id')
                ->toArray();

            foreach ($listIds as $position => $listId) {
                BoardList::where('id', $listId)
                        ->update(['position' => $position + 1]);
            }

            $lists = BoardList::where('board_id', $this->board->id)
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

            broadcast(new ListReordered($this->board->id, $lists))->toOthers();

            $listNames = BoardList::whereIn('id', array_merge($oldOrder, $listIds))
                ->pluck('name', 'id')
                ->toArray();
            $oldNames = array_map(fn($id) => $listNames[$id] ?? $id, $oldOrder);
            $newNames = array_map(fn($id) => $listNames[$id] ?? $id, $listIds);

            if ($oldOrder !== $listIds) {
                $this->logActivity('reorder_list', 'Urutan list diubah pada board "' . ($this->board->title ?? '-') . '". Sebelumnya: [' . implode(', ', $oldNames) . '] Menjadi: [' . implode(', ', $newNames) . ']', $this->board);
            }
        });
    }
}
