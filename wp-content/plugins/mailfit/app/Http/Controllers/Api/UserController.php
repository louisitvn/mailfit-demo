<?php

namespace Acelle\Http\Controllers\Api;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

/**
 * /api/v1/users - API controller for managing users.
 */
class UserController extends Controller
{        
    /**
     * Create new user.
     *
     * POST /api/v1/users
     *
     * @param \Illuminate\Http\Request $request All users information.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $current_user = \Auth::guard('api')->user();
        
        $user = new \Acelle\Model\User();

        // authorize
        if (!$current_user->can('create', $user)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }
        
        // save posted data
        if ($request->isMethod('post')) {
            $validator = \Validator::make($request->all(), $user->apiRules());    
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }

            // Save current user info
            $user->fill($request->all());
            $user->user_group_id = $request->user_group_id;
            $user->user_id = $current_user->id;
            $user->status = 'active';

            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            if ($user->save()) {
                // Upload and save image
                if ($request->hasFile('image')) {
                    if ($request->file('image')->isValid()) {
                        // Remove old images
                        $user->removeImage();
                        $user->image = $user->uploadImage($request->file('image'));
                        $user->save();
                    }
                }

                // Remove image
                if ($request->_remove_image == 'true') {
                    $user->removeImage();
                    $user->image = '';
                }

                return \Response::json([
                    'message' => trans('messages.user.created'),
                    'api_token' => $user->api_token
                ], 200);
            }
        }
    }
    
    /**
     * Update user information.
     *
     * PATCH /api/v1/users
     *
     * @param \Illuminate\Http\Request $request All users information.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $current_user = \Auth::guard('api')->user();
        
        $user = \Acelle\Model\User::findByUid($id);

        // authorize
        if (!$current_user->can('update', $user)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }
        
        // save posted data
        if ($request->isMethod('patch')) {
            $validator = \Validator::make($request->all(), $user->apiUpdateRules($request));    
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }
            
            // Save current user info
            $user->fill($request->all());
            
            // Change group
            if(isset($request->user_group_id) && $current_user->can('change_group', $user)) {
                $user->user_group_id = $request->user_group_id;
            }

            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    // Remove old images
                    $user->removeImage();
                    $user->image = $user->uploadImage($request->file('image'));
                }
            }

            // Remove image
            if ($request->_remove_image == 'true') {
                $user->removeImage();
                $user->image = '';
            }

            if ($user->save()) {
                return \Response::json([
                    'message' => trans('messages.user.created'),
                    'api_token' => $user->api_token
                ], 200);
            }
        }
    }
}
