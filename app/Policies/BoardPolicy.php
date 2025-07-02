<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return bool
     */
    public function view(User $user, Board $board)
    {
        return $user->id === $board->user_id || $board->collaborators()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return bool
     */
    public function update(User $user, Board $board)
    {
        return $user->id === $board->user_id || $board->collaborators()->where('user_id', $user->id)->where('role', 'edit')->exists();
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return bool
     */
    public function delete(User $user, Board $board)
    {
        return $user->id === $board->user_id;
    }
}
