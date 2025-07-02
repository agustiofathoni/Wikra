<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListRequest;
use App\Http\Requests\UpdateListRequest;
use App\Models\Board;
use App\Models\BoardList;
use App\Services\BoardListService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BoardListController extends Controller
{
    protected $boardListService;

    public function __construct(BoardListService $boardListService)
    {
        $this->boardListService = $boardListService;
    }

    public function store(StoreListRequest $request, Board $board)
    {
        $this->boardListService->createList($board, $request->validated());

        return redirect()->route('boards.show', $board)->with('list_success', 'List created successfully');
    }

    public function update(UpdateListRequest $request, BoardList $list)
    {
        $this->boardListService->updateList($list, $request->validated());

        return redirect()->back()->with('list_success', 'List updated successfully');
    }

    public function destroy(BoardList $list)
    {
        $this->authorize('update', $list->board);

        $this->boardListService->deleteList($list);

        return redirect()->route('boards.show', $list->board)->with('list_success', 'List deleted successfully');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'lists' => 'required|array',
            'lists.*' => 'numeric|exists:lists,id'
        ]);

        $this->boardListService->reorderLists($validated['lists']);

        return response()->json(['success' => true]);
    }
}


