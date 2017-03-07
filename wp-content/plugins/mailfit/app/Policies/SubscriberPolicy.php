<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriberPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        return $item->mailList->user_id == $user->id || $user->userGroup->backend_access;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $max = $user->getOption('frontend', 'subscriber_max');
        $max_per_list = $user->getOption('frontend', 'subscriber_per_list_max');

        return $user->id == $item->mailList->user_id &&
            ($max > $user->subscribers()->count() || $max == -1) &&
            ($max_per_list > $item->mailList->subscribers()->count() || $max_per_list == -1);
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        return $item->mailList->user_id == $user->id;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        return $item->mailList->user_id == $user->id;
    }

    public function subscribe(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        return $item->mailList->user_id == $user->id && $item->status == 'unsubscribed';
    }

    public function unsubscribe(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        return $item->mailList->user_id == $user->id && $item->status == 'subscribed';
    }
}
