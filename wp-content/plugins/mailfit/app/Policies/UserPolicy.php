<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }
    
    public function read(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        return $user->id == $item->id || $user->id == $item->user_id;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $can = $user->userGroup->backend_access && $user->getOption('backend', 'user_create') == 'yes';

        return $can;
    }

    public function profile(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        return $user->id == $item->id;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->userGroup->backend_access && $user->getOption('backend', 'user_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->userGroup->backend_access && $user->getOption('backend', 'user_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);
        $can = $can && $item->lists()->count() == 0 && $item->user_id != null;

        return $can;
    }

    public function switch_user(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->userGroup->backend_access && $user->getOption('backend', 'user_switch');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);
        $can = $can && $item->id != $user->id;

        return $can;
    }
    
    public function change_group(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->userGroup->backend_access && $user->getOption('backend', 'user_update');
        $can = $ability == 'all';

        return $can;
    }
}
