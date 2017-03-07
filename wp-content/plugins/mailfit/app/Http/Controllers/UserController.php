<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    /**
     * Render user image.
     */
    public function avatar(Request $request)
    {
        // Get current user
        if ($request->uid != '0') {
            $user = \Acelle\Model\User::findByUid($request->uid);
        } else {
            $user = new \Acelle\Model\User();
        }
        if (!empty($user->imagePath())) {
            $img = \Image::make($user->imagePath());
        } else {
            $img = \Image::make(public_path('assets/images/placeholder.jpg'));
        }

        return $img->response();
    }
    
    /**
     * User uid for editor
     */
    public function showUid(Request $request)
    {
        $user = $request->user();
        echo $user->uid;
    }
    
    /**
     * Log in back user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function loginBack(Request $request)
    {
        $id = \Session::pull('orig_user_id');
        $orig_user = \Acelle\Model\User::findByUid($id);
        
        \Auth::login($orig_user);
        
        return redirect()->action('Admin\UserController@index');
    }
}
