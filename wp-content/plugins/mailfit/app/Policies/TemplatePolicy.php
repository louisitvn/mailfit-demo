<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return true;
    }

    public function view(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return $item->user_id == $user->id || $item->shared == true;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return $item->user_id == $user->id;
    }

    public function image(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return $item->user_id == $user->id || $item->shared == true;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return $item->user_id == $user->id;
    }

    public function preview(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return $item->user_id == $user->id || $item->shared == true;
    }

    public function saveImage(\Acelle\Model\User $user, \Acelle\Model\Template $item)
    {
        return $item->user_id == $user->id || $item->shared == true;
    }
}
