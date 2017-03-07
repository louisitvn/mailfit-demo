<?php

namespace Acelle\Http\Controllers\Api;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

/**
 * /api/v1/lists/{list_id}/subscribers - API controller for managing list's subscribers.
 */
class SubscriberController extends Controller
{
    /**
     * Display all list's subscribers.
     *
     * GET /api/v1/lists/{list_id}/subscribers
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $list_id List's id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $list_id)
    {
        $user = \Auth::guard('api')->user();
        $list = \Acelle\Model\MailList::findByUid($list_id);
        
        // authorize
        if (!is_object($list)) {
            return \Response::json(array('message' => trans('List not found')), 404);
        }

        // authorize
        if (!$user->can('read', $list)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        if (isset($request->per_page)) {
            $per_page = $request->per_page;
        } else {
            $per_page = \Acelle\Model\Subscriber::$itemsPerPage;
        }

        $items = \Acelle\Model\Subscriber::search($request, $user)
            ->where('mail_list_id', '=', $list->id)
            ->groupBy('subscribers.id')
            ->paginate($per_page);

        $subscribers = [];
        foreach ($items as $item) {
            $row = [
                'uid' => $item->uid,
                'email' => $item->email,
                'status' => $item->status,
            ];

            foreach ($list->fields as $field) {
                if ($field->tag != 'EMAIL') {
                    $row[$field->tag] = $item->getValueByField($field);
                }
            }

            $subscribers[] = $row;
        }

        return \Response::json($subscribers, 200);
    }
    
    /**
     * Create subscriber for a mail list.
     *
     * GET /api/v1/lists/{list_id}/subscribers/store
     *
     * @param \Illuminate\Http\Request $request All subscriber information: EMAIL (required), FIRST_NAME (?), LAST_NAME (?),... (depending on the list fields configuration)
     * @param string                   $list_id List's id
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $list_id)
    {
        $user = \Auth::guard('api')->user();
        $list = \Acelle\Model\MailList::findByUid($list_id);
        
        // authorize
        if (!is_object($list)) {
            return \Response::json(array('message' => trans('List not found')), 404);
        }
        
        $subscriber = new \Acelle\Model\Subscriber();
        $subscriber->mail_list_id = $list->id;
        $subscriber->user_id = $user->id;
        $subscriber->status = 'subscribed';
        $subscriber->from = 'api';

        // authorize
        if (!$user->can('create', $subscriber)) {
            return \Response::json(array('message' => trans('no_more_item')), 403);
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            $validator = \Validator::make($request->all(), $subscriber->getRules());    
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }

            // Save subscriber
            $subscriber->email = $request->EMAIL;
            $subscriber->save();
            
            // Update field
            $subscriber->updateFields($request->all());
            
            // Log
            $subscriber->log('created', $user);
            
            return \Response::json(array(
                'message' => trans('messages.subscriber.created'),
                'subscriber_uid' => $subscriber->uid
            ), 200);
        }
    }

    /**
     * Display the specified subscriber information.
     *
     * GET /api/v1/lists/{list_id}/subscribers/{id}
     *
     * @param string $list_id List's id
     * @param string $id      Subsciber's id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($list_id, $id)
    {
        $user = \Auth::guard('api')->user();

        $list = \Acelle\Model\MailList::findByUid($list_id);
        
        // authorize
        if (!is_object($list)) {
            return \Response::json(array('message' => trans('List not found')), 404);
        }
        
        $item = \Acelle\Model\Subscriber::
            where('uid', '=', $id)
            ->first();

        // authorize
        if (!$user->can('read', $item)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // check if item exists
        if (!is_object($item)) {
            return \Response::json(array('message' => 'Item not found'), 404);
        }

        // subscriber
        $subscriber = [
            'uid' => $item->uid,
            'email' => $item->email,
        ];

        foreach ($list->fields as $field) {
            if ($field->tag != 'EMAIL') {
                $subscriber[$field->tag] = $item->getValueByField($field);
            }
        }

        return \Response::json(['subscriber' => $subscriber], 200);
    }

    /**
     * Subscribe a subscriber.
     *
     * PATCH /api/v1/lists/{list_id}/subscribers/{id}/subscribe
     *
     * @param string $list_id List's id
     * @param string $id      Subsciber's id
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe($list_id, $id)
    {
        $user = \Auth::guard('api')->user();
        
        $list = \Acelle\Model\MailList::findByUid($list_id);
        
        // authorize
        if (!is_object($list)) {
            return \Response::json(array('message' => trans('List not found')), 404);
        }
        
        $subscriber = \Acelle\Model\Subscriber::findByUid($id);

        // check if item exists
        if (!is_object($subscriber)) {
            return \Response::json(array('message' => 'Item not found'), 404);
        }
        
        // check if item subscribed
        if ($subscriber->status == 'subscribed') {
            return \Response::json(array('message' => 'Already subscribed'), 409);
        }

        // authorize
        if (!$user->can('subscribe', $subscriber)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // Unsubscribe
        $subscriber->status = 'subscribed';
        $subscriber->save();

        return \Response::json(array('message' => 'Subscribed'), 200);
    }

    /**
     * Unsubscribe a subscriber.
     *
     * PATCH /api/v1/lists/{list_id}/subscribers/{id}/unsubscribe
     *
     * @param string $list_id List's id
     * @param string $id      Subsciber's id
     *
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe($list_id, $id)
    {
        $user = \Auth::guard('api')->user();
        
        $list = \Acelle\Model\MailList::findByUid($list_id);
        
        // authorize
        if (!is_object($list)) {
            return \Response::json(array('message' => trans('List not found')), 404);
        }
        
        $subscriber = \Acelle\Model\Subscriber::findByUid($id);

        // check if item exists
        if (!is_object($subscriber)) {
            return \Response::json(array('message' => 'Item not found'), 404);
        }
        
        // check if item unsubscribed
        if ($subscriber->status == 'unsubscribed') {
            return \Response::json(array('message' => 'Already unsubscribed'), 409);
        }

        // authorize
        if (!$user->can('unsubscribe', $subscriber)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // Unsubscribe
        $subscriber->status = 'unsubscribed';
        $subscriber->save();

        return \Response::json(array('message' => 'Unsubscribed'), 200);
    }

    /**
     * Delete a subscriber.
     *
     * DELETE /api/v1/lists/{list_id}/subscribers/{id}/delete
     *
     * @param string $list_id List's id
     * @param string $id      Subsciber's id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($list_id, $id)
    {
        $user = \Auth::guard('api')->user();
        
        $list = \Acelle\Model\MailList::findByUid($list_id);
        
        // authorize
        if (!is_object($list)) {
            return \Response::json(array('message' => trans('List not found')), 404);
        }
        
        $subscriber = \Acelle\Model\Subscriber::findByUid($id);

        // check if item exists
        if (!is_object($subscriber)) {
            return \Response::json(array('message' => 'Item not found'), 404);
        }

        // authorize
        if (!$user->can('delete', $subscriber)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // Unsubscribe
        $subscriber->delete();

        return \Response::json(array('message' => 'Deleted'), 200);
    }
}
