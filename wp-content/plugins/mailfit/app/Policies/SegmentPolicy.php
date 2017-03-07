<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SegmentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Segment $item)
    {
        $max_per_list = $user->getOption('frontend', 'segment_per_list_max');

        return $user->id == $item->mailList->user_id && ($max_per_list > $item->mailList->segments()->count() || $max_per_list == -1);
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Segment $item)
    {
        return $item->mailList->user_id == $user->id;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Segment $item)
    {
        return $item->mailList->user_id == $user->id;
    }
}
