<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SendingServerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item)
    {
        $can = $user->getOption('backend', 'sending_server_create') == 'yes';

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item)
    {
        $ability = $user->getOption('backend', 'sending_server_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item)
    {
        $ability = $user->getOption('backend', 'sending_server_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);

        return $can;
    }
    
    public function disable(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item)
    {
        $ability = $user->getOption('backend', 'sending_server_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);

        return ($can && $item->status != "inactive");
    }
    
    public function enable(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item)
    {
        $ability = $user->getOption('backend', 'sending_server_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);

        return ($can && $item->status != "active");
    }
}
