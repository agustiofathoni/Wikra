<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use App\Models\Collaborator;
use Illuminate\Http\Request;


class BoardCollaboratorController extends Controller
{
    // Invite user by email
    public function invite(Request $request, Board $board)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()
                ->route('boards.show', [$board, 'invite' => 1])
                ->with('error', 'Email tidak ditemukan.');
        }

        if ($user->id == $board->user_id) {
            return back()->with('error', 'Tidak bisa mengundang diri sendiri.');
        }

        // Cek apakah sudah pernah diundang
        $collab = Collaborator::where('board_id', $board->id)
            ->where('user_id', $user->id)
            ->first();

        if ($collab) {
            if ($collab->status === 'declined') {
                // Update status jadi pending jika sebelumnya declined
                $collab->update(['status' => 'pending']);
                return redirect()
                    ->route('boards.show', [$board, 'invite' => 1])
                    ->with('success', 'Undangan berhasil dikirim ulang.');
            } else {
                return back()->with('error', 'User sudah diundang atau sudah menjadi kolaborator.');
            }
        } else {
            // Belum pernah diundang, buat baru
            Collaborator::create([
                'board_id' => $board->id,
                'user_id' => $user->id,
                'status' => 'pending'
            ]);
            return redirect()
                ->route('boards.show', [$board, 'invite' => 1])
                ->with('success', 'Undangan berhasil dikirim.');
        }
    }

    // Approve invitation
    public function approve(Collaborator $collaborator)
    {
        $this->authorize('update', $collaborator); // pastikan user yang login adalah yang diundang
        $collaborator->update(['status' => 'accepted']);
        return back()->with('success', 'Anda telah bergabung ke board.');
    }

    // Decline invitation
    public function decline(Collaborator $collaborator)
    {
        $this->authorize('update', $collaborator);
        $collaborator->update(['status' => 'declined']);
        return back()->with('success', 'Anda menolak undangan.');
    }

    // Remove collaborator (hanya owner board)
    public function remove(Board $board, Collaborator $collaborator)
    {
        $this->authorize('update', $board); // hanya owner
        $collaborator->delete();
        return back()->with('success', 'Kolaborator dihapus.');
    }
}
