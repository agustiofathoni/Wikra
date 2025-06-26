<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
       $user = Auth::user();

        // My Boards (yang dibuat user)
        $boards = $user->boards;

        // Board Collaborator (user sebagai kolaborator dan sudah accepted)
        $collaboratorBoards = \App\Models\Board::whereHas('collaborators', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'accepted');
        })->get();

        // Board Invitation (undangan pending)
        $pendingInvites = \App\Models\Collaborator::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('board.user')
            ->get();

        return view('dashboard', compact('boards', 'collaboratorBoards', 'pendingInvites'));
    }

    public function show(Board $board)
    {
         $user = Auth::user();

    // Izinkan jika owner atau kolaborator accepted
        $isOwner = $board->user_id === $user->id;
        $isCollaborator = $board->collaborators()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        if (!($isOwner || $isCollaborator)) {
            abort(403);
        }
        $board->load(['collaborators.user']);

        $listIds = $board->lists()->pluck('id');
        $taskIds = \App\Models\Task::whereIn('list_id', $listIds)->pluck('id');
        // ------------------------

       $activities = ActivityLog::with('user')
        ->where('board_id', $board->id)
        ->orderByDesc('created_at')
        ->limit(20)
        ->get();

        return view('boards.show', compact('board', 'activities'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $board = new Board();
        $board->title = $validated['title'];
        $board->description = $validated['description'] ?? null;
        $board->user_id = Auth::id();
        $board->save();

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board created successfully');
    }

    public function update(Request $request, Board $board)
    {
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Board updated successfully.');
    }

    public function destroy(Board $board)
    {
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }
        $board->delete();
        return redirect()->route('dashboard')->with('success', 'Board deleted successfully.');
    }
}
