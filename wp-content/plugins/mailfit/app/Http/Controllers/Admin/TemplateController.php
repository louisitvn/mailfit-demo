<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

class TemplateController extends Controller
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
        if ($request->user()->getOption('backend', 'template_read') == 'no') {
            return $this->notAuthorized();
        }

        $templates = \Acelle\Model\Template::getAll();

        return view('admin.templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        if ($request->user()->getOption('backend', 'template_read') == 'no') {
            return $this->notAuthorized();
        }

        $templates = \Acelle\Model\Template::adminSearch($request)->paginate($request->per_page);

        return view('admin.templates._list', [
            'templates' => $templates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Generate info
        $user = $request->user();
        $template = new \Acelle\Model\Template();

        // authorize
        if ($request->user()->getOption('backend', 'template_create') == 'no') {
            return $this->notAuthorized();
        }

        // Get old post values
        if (null !== $request->old()) {
            $template->fill($request->old());
        }

        return view('admin.templates.create', [
            'template' => $template,
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
        // Generate info
        $user = $request->user();
        $template = new \Acelle\Model\Template();
        $template->user_id = $request->user()->id;
        $template->shared = true;

        // authorize
        if ($request->user()->getOption('backend', 'template_create') == 'no') {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            $rules = array(
                'name' => 'required',
                'content' => 'required',
            );

            $this->validate($request, $rules);

            // Save template
            $template->fill($request->all());
            $template->source = 'editor';
            if(isset($request->source)) {
                $template->source = $request->source;
            }

            $template->save();

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.template.created'));

            return redirect()->action('Admin\TemplateController@index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $uid)
    {
        // Generate info
        $user = $request->user();
        $template = \Acelle\Model\Template::findByUid($uid);

        // authorize
        if ($request->user()->getOption('backend', 'template_update') == 'no'
            || ($request->user()->getOption('backend', 'template_update') == 'own' && $template->user_id != $user->id)
        ) {
            return $this->notAuthorized();
        }

        // Get old post values
        if (null !== $request->old()) {
            $template->fill($request->old());
        }

        return view('admin.templates.edit', [
            'template' => $template,
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
        // Generate info
        $user = $request->user();
        $template = \Acelle\Model\Template::findByUid($request->uid);

        // authorize
        if ($request->user()->getOption('backend', 'template_update') == 'no'
            || ($request->user()->getOption('backend', 'template_update') == 'own' && $template->user_id != $user->id)
        ) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            $rules = array(
                'name' => 'required',
                'content' => 'required',
            );

            $this->validate($request, $rules);

            // Save template
            $template->fill($request->all());
            $template->save();

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.template.updated'));

            return redirect()->action('Admin\TemplateController@index');
        }
    }

    /**
     * Upload template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $user = $request->user();

        // Create template
        $template = new \Acelle\Model\Template();
        $template->user_id = $user->id;
        $template->shared = true;

        // authorize
        if ($request->user()->getOption('backend', 'template_create') == 'no') {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            $zip = new \ZipArchive();

            $rules = array(
                'file' => 'required',
                'name' => 'required',
            );

            // check if file exsits
            if (!$request->hasFile('file')) {
                $rules['file_not_found'] = 'required';
            }
            $this->validate($request, $rules);

            // check if file is zip achive
            $file_ext = $request->file('file')->guessExtension();

            if ($file_ext != 'zip') {
                $rules['not_zip_archive'] = 'required';
            }
            $this->validate($request, $rules);

            // move file to temp place
            $tmp_path = storage_path('tmp/uploaded_template_'.$user->id.'_'.time());
            $file_name = $request->file('file')->getClientOriginalName();
            $request->file('file')->move($tmp_path, $file_name);
            $tmp_zip = $tmp_path.'/'.$file_name;

            // read zip file check if zip archive invalid
            if ($zip->open($tmp_zip, \ZipArchive::CREATE) !== true) {
                $rules['zip_archive_unvalid'] = 'required';
            }
            $this->validate($request, $rules);

            // unzip template archive and remove zip file
            $zip->extractTo($tmp_path);
            $zip->close();
            unlink($tmp_zip);

            // try to find the main file, index.html | index.html | file_name.html | ...
            $archive_name = str_replace(array('../', './', '..\\', '.\\', '..'), '', basename($file_name, '.zip'));
            $main_file = null;
            $sub_path = "";
            $possible_main_file_names = array('index.html', 'index.htm', $archive_name.'.html', $archive_name.'.htm');
            foreach ($possible_main_file_names as $name) {
                if (is_file($file = $tmp_path.'/'.$name)) {
                    $main_file = $file;
                    break;
                }
                $dirs = array_filter(glob($tmp_path.'/'.'*'), 'is_dir');
                foreach($dirs as $sub) {
                    if (is_file($file = $sub.'/'.$name)) {
                        $main_file = $file;
                        $sub_path = explode("/",$sub)[count(explode("/",$sub))-1]."/";
                        break;
                    }
                }
            }
            // try to find first htm|html file
            if ($main_file === null) {
                $objects = scandir($tmp_path);
                foreach ($objects as $object) {
                    if ($object != '.' && $object != '..') {
                        if (!is_dir($tmp_path.'/'.$object)) {
                            if (preg_match('/\.html?$/i', $object)) {
                                $main_file = $tmp_path.'/'.$object;
                                break;
                            }
                        }
                    }
                }
                $dirs = array_filter(glob($tmp_path.'/'.'*'), 'is_dir');
                foreach($dirs as $sub) {
                    $objects = scandir($sub);
                    foreach ($objects as $object) {
                        if ($object != '.' && $object != '..') {
                            if (!is_dir($sub.'/'.$object)) {
                                if (preg_match('/\.html?$/i', $object)) {
                                    $main_file = $sub.'/'.$object;
                                    $sub_path = explode("/",$sub)[count(explode("/",$sub))-1]."/";
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            // main file not found
            if ($main_file === null) {
                $rules['main_file_not_found'] = 'required';
            }
            $this->validate($request, $rules);

            // read main file content
            $html_content = trim(file_get_contents($main_file));
            if (empty($html_content)) {
                $rules['main_file_empty'] = 'required';
            }
            $this->validate($request, $rules);

            // Save new template
            $template->fill($request->all());
            $template->content = $html_content;
            $template->save();

            // copy all folder to public path
            $public_dir = 'upload/templates/template_'.$template->uid;
            $public_upload_path = public_path($public_dir);
            if (!file_exists($public_upload_path)) {
                mkdir($public_upload_path, 0777, true);
            }
            // exec("cp -r {$tmp_path}/* {$public_upload_path}/");
            \Acelle\Library\Tool::xcopy($tmp_path, $public_upload_path);

            // find all link in html content
            preg_match_all('/(?<=src=|background=|href=|url\()(\'|")?(?<url>.*?)(?=\1|\))/i', $template->content, $matches);
            $srcs = array_unique($matches['url']);

            foreach ($srcs as $src) {
                if (preg_match('/^https?/i', $src) || preg_match('/^#/i', $src) || strpos($src, '//') === 0) {
                    continue;
                }

                $new_src = $request->root().'/'.$public_dir.'/'.$sub_path.$src;

                // replace image url
                $template->content = str_replace($src, $new_src, $template->content);
                $template->save();
            }

            // remove tmp folder
            //exec("rm -r {$tmp_path}");
            \Acelle\Library\Tool::xdelete($tmp_path);

            $request->session()->flash('alert-success', trans('messages.template.uploaded'));

            return redirect()->action('Admin\TemplateController@index');
        }

        return view('admin.templates.upload', [
            'template' => $template,
        ]);
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
        $items = \Acelle\Model\Template::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (
                $request->user()->getOption('backend', 'template_delete') == 'no'
                || ($request->user()->getOption('backend', 'template_delete') == 'own' && $item->user_id != $request->user()->id)
            ) {
                return;
            }
        }

        foreach ($items->get() as $item) {
            $item->delete();
        }

        // Redirect to my lists page
        echo trans('messages.templates.deleted');
    }
    
    /**
     * Buiding email template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function build(Request $request)
    {
        $template = new \Acelle\Model\Template();
        $template->name = trans('messages.untitled_template');
        
        // authorize
        if (\Gate::denies('create', $template)) {
            return $this->notAuthorized();
        }
        
        $elements = [];
        if(isset($request->style)) {
            $elements = \Acelle\Model\Template::templateStyles()[$request->style];
        }
        
        return view('admin.templates.build', [
            'template' => $template,
            'elements' => $elements
        ]);
    }
    
    /**
     * Buiding email template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function rebuild(Request $request)
    {
        // Generate info
        $user = $request->user();
        $template = \Acelle\Model\Template::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $template)) {
            return $this->notAuthorized();
        }

        return view('admin.templates.rebuild', [
            'template' => $template,
        ]);
    }
    
    /**
     * Select template style.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function buildSelect(Request $request)
    {
        $template = new \Acelle\Model\Template();
        
        return view('admin.templates.build_start', [
            'template' => $template,
        ]);
    }
}
