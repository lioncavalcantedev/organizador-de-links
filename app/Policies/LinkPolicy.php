<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;

class LinkPolicy
{
    public function move(User $user, Link $link): bool
    {
        return $link->user_id === $user->id;
    }
}
