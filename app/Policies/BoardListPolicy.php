<?php

namespace App\Policies;

use App\Models\BoardList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardListPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BoardList  $list
     * @return bool
     */
    public function update(User $user, BoardList $list)
    {
        return $user->can('update', $list->board);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BoardList  $list
     * @return bool
     */
    public function delete(User $user, BoardList $list)
    {
        return $user->can('update', $list->board);
    }
}
