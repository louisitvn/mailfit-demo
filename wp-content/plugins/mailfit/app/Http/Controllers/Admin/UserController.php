<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->getOption('backend', 'user_read') == 'no') {
            return $this->notAuthorized();
        }

        $users = \Acelle\Model\User::getAll();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        if ($request->user()->getOption('backend', 'user_read') == 'no') {
            return $this->notAuthorized();
        }

        $users = \Acelle\Model\User::search($request)->paginate($request->per_page);

        return view('admin.users._list', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = new \Acelle\Model\User();
        $user->status = 'active';
        $user->uid = '0';
        $user->fill($request->old());

        // authorize
        if (\Gate::denies('create', $user)) {
            return $this->notAuthorized();
        }

        return view('admin.users.create', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get current user
        $current_user = $request->user();
        $user = new \Acelle\Model\User();
        $contact = new \Acelle\Model\Contact();

        // authorize
        if (\Gate::denies('create', $user)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, $user->newRules());

            // Save current user info
            $user->fill($request->all());
            $user->user_group_id = $request->user_group_id;
            $user->user_id = $request->user()->id;
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

                $request->session()->flash('alert-success', trans('messages.user.created'));

                return redirect()->action('Admin\UserController@index');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $user = \Acelle\Model\User::findByUid($id);

        // authorize
        if (\Gate::denies('update', $user)) {
            return $this->notAuthorized();
        }

        $user->fill($request->old());

        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get current user
        $current_user = $request->user();
        $user = \Acelle\Model\User::findByUid($id);

        // authorize
        if (\Gate::denies('update', $user)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('patch')) {
            
            if($this->isDemoMode()) {
                return $this->notAuthorized();
            }
            
            $this->validate($request, $user->rules());
            
            // Save current user info
            $user->fill($request->all());
            
            // Change group
            $user->user_group_id = $request->user_group_id;

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
                $request->session()->flash('alert-success', trans('messages.user.updated'));

                return redirect()->action('Admin\UserController@index');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function select2(Request $request)
    {
        $result = [['id' => '1', 'text' => 'One'], ['id' => '2', 'text' => 'Two']];

        return response()->json($result);
    }

    /**
     * Enable item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $user = \Acelle\Web\User::find($request->id);

        $user->status = 'active';
        $user->save();

        echo '<p class="alert alert-success">'.trans('messages.user.enabled').'<p>';
    }

    /**
     * Disable item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $user = \Acelle\Web\User::find($request->id);

        $user->status = 'inactive';
        $user->save();

        echo '<p class="alert alert-success">'.trans('messages.user.disabled').'<p>';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $items = \Acelle\Model\User::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::denies('delete', $item)) {
                return;
            }
        }

        foreach ($items->get() as $item) {
            $item->delete();
        }

        // Redirect to my lists page
        echo trans('messages.users.deleted');
    }

    /**
     * Switch user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function switch_user(Request $request)
    {
        $user = \Acelle\Model\User::findByUid($request->uid);

        // authorize
        if (\Gate::denies('switch_user', $user)) {
            return;
        }
        
        $orig_id = $request->user()->uid;
        \Auth::login($user);
        \Session::put('orig_user_id', $orig_id);

        return redirect()->action('HomeController@index');
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
}
