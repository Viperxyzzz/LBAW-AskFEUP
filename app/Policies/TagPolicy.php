<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tag;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class TagPolicy
{
    use HandlesAuthorization;

}
