<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id || $user->userGroup->backend_access;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        $max = $user->getOption('frontend', 'campaign_max');

        return $max > $user->campaigns()->count() || $max == -1;
    }

    public function overview(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id && $item->status != 'new';
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id
            && ($item->is_auto || in_array($item->status, ['new', 'ready', 'error']));
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id && in_array($item->status, ['new', 'ready', 'paused', 'done', 'sending', 'error']);
    }
    
    public function pause(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id && in_array($item->status, ['sending', 'ready']);
    }
    
    public function restart(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id && in_array($item->status, ['paused', 'error']);
    }
    
    public function sort(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id;
    }
    
    public function copy(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id;
    }
    
    public function preview(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id;
    }
    
    public function saveImage(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id;
    }
    
    public function image(\Acelle\Model\User $user, \Acelle\Model\Campaign $item)
    {
        return $item->user_id == $user->id;
    }
}
