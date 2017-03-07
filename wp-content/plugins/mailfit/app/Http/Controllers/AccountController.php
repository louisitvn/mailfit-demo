<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
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
     * Update user profile.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function profile(Request $request)
    {
        // Get current user
        $user = $request->user();
        $user->getFrontendScheme();
        $user->getBackendScheme();

        // Authorize
        if (\Gate::denies('profile', $user)) {
            return $this->notAuthorized();
        }

        // Save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, $user->rules());
            
            if($this->isDemoMode()) {
                return $this->notAuthorized();
            }

            // Save current user info
            $user->fill($request->all());

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
                $request->session()->flash('alert-success', trans('messages.profile.updated'));
            }
        }

        return view('account.profile', [
            'user' => $user->fill($request->old()),
        ]);
    }

    /**
     * Update user contact information.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function contact(Request $request)
    {        
        // Get current user
        $user = $request->user();
        if (is_object($user->contact)) {
            $contact = $user->contact;
        } else {
            $contact = new \Acelle\Model\Contact([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]);
        }
        // var_dump((array) $user->attributes);

        // Create new company if null
        if (!is_object($contact)) {
            $contact = new \Acelle\Model\Contact();
            $contact->user_id = $user->id;
        }

        // authorize
        if (\Gate::denies('update', $contact)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, \Acelle\Model\Contact::$rules);

            $contact->fill($request->all());

            // Save current user info
            if ($contact->save()) {
                if (is_object($contact)) {
                    $user->contact_id = $contact->id;
                    $user->save();
                }
                $request->session()->flash('alert-success', 'Contact information was successfully updated!');
            }
        }

        return view('account.contact', [
            'user' => $user,
            'contact' => $contact->fill($request->old()),
        ]);
    }

    /**
     * User logs.
     */
    public function logs(Request $request)
    {
        $logs = $request->user()->logs;

        return view('account.logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Logs list.
     */
    public function logsListing(Request $request)
    {
        $logs = \Acelle\Model\Log::search($request)->paginate($request->per_page);

        return view('account.logs_listing', [
            'logs' => $logs,
        ]);
    }

    /**
     * Quta logs.
     */
    public function quotaLog(Request $request)
    {
        return view('account.quota_log');
    }

    /**
     * Api token.
     */
    public function api(Request $request)
    {
        return view('account.api');
    }

    /**
     * Renew api token.
     */
    public function renewToken(Request $request)
    {
        $user = $request->user();

        $user->api_token = str_random(60);
        $user->save();

        // Redirect to my lists page
        $request->session()->flash('alert-success', trans('messages.user_api.renewed'));

        return redirect()->action('AccountController@api');
    }
}
