<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

class SegmentController extends Controller
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
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segments = $list->segments;

        return view('segments.index', [
            'segments' => $segments,
            'list' => $list,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segments = \Acelle\Model\Segment::search($request)->paginate($request->per_page);

        return view('segments._list', [
            'segments' => $segments,
            'list' => $list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segment = new \Acelle\Model\Segment();
        $segment->mail_list_id = $list->id;

        // authorize
        if (\Gate::denies('create', $segment)) {
            return $this->noMoreItem();
        }

        // Get old post values
        if (isset($request->old()['conditions'])) {
            $segment->fill($request->old());

            $segment->segmentConditions = collect();
            foreach ($request->old()['conditions'] as $key => $item) {
                $condition = new \Acelle\Model\SegmentCondition();
                $condition->uid = $key;
                $condition->fill($item);
                $segment->segmentConditions->push($condition);
            }
        }

        return view('segments.create', [
            'list' => $list,
            'segment' => $segment,
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
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segment = new \Acelle\Model\Segment();
        $segment->mail_list_id = $list->id;

        // authorize
        if (\Gate::denies('create', $segment)) {
            return $this->noMoreItem();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            $rules = \Acelle\Model\Segment::$rules;

            // addtion validates
            $empty = false;
            if (isset($request->conditions)) {
                foreach ($request->conditions as $key => $param) {
                    $rules['conditions.'.$key.'.field_id'] = 'required';
                    $rules['conditions.'.$key.'.operator'] = 'required';
                    $rules['conditions.'.$key.'.value'] = 'required';
                }
            } else {
                $empty = true;
            }
            if ($empty) {
                $rules['segment_conditions_empty'] = 'required';
            }

            // Check validation
            $this->validate($request, $rules);

            // Save segment
            $segment->fill($request->all());
            $segment->save();
            // save conditions
            foreach ($request->conditions as $key => $param) {
                $condition = new \Acelle\Model\SegmentCondition();
                $condition->fill($param);
                $condition->segment_id = $segment->id;
                $condition->field_id = \Acelle\Model\Field::findByUid($param['field_id'])->id;

                $condition->save();
            }

            // Log
            $segment->log('created', $request->user());

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.segment.created'));

            return redirect()->action('SegmentController@index', $list->uid);
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
     * Display segment's subscribers.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribers(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segment = \Acelle\Model\Segment::findByUid($request->uid);

        return view('segments.subscribers', [
            'subscribers' => $segment->subscribers(),
            'list' => $list,
            'segment' => $segment,
        ]);
    }

    /**
     * Display segment's subscribers.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function listing_subscribers(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segment = \Acelle\Model\Segment::findByUid($request->uid);

        $subscribers = $segment->subscribers($request)->paginate($request->per_page);
        $fields = $list->getFields->whereIn('uid', explode(',', $request->columns));

        return view('subscribers._list', [
            'subscribers' => $subscribers,
            'list' => $list,
            'fields' => $fields,
            'segment' => $segment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segment = \Acelle\Model\Segment::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $segment)) {
            return $this->notAuthorized();
        }

        // Get old post values
        if (isset($request->old()['conditions'])) {
            $segment->fill($request->old());

            $segment->segmentConditions = collect([]);
            foreach ($request->old()['conditions'] as $key => $item) {
                $condition = new \Acelle\Model\SegmentCondition();
                $condition->uid = $key;
                $condition->fill($item);
                $segment->segmentConditions->push($condition);
            }
        }

        return view('segments.edit', [
            'list' => $list,
            'segment' => $segment,
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
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $segment = \Acelle\Model\Segment::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $segment)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            $rules = \Acelle\Model\Segment::$rules;

            // addtion validates
            $empty = false;
            if (isset($request->conditions)) {
                foreach ($request->conditions as $key => $param) {
                    $rules['conditions.'.$key.'.field_id'] = 'required';
                    $rules['conditions.'.$key.'.operator'] = 'required';
                    if (!in_array($param['operator'], ['blank', 'not_blank'])) {
                        $rules['conditions.'.$key.'.value'] = 'required';
                    }
                }
            } else {
                $empty = true;
            }
            if ($empty) {
                $rules['segment_conditions_empty'] = 'required';
            }

            // Check validation
            $this->validate($request, $rules);

            // Save segment
            $segment->fill($request->all());
            $segment->save();
            // save conditions
            $segment->segmentConditions()->delete();
            foreach ($request->conditions as $key => $param) {
                $condition = new \Acelle\Model\SegmentCondition();
                $condition->fill($param);
                $condition->segment_id = $segment->id;
                $condition->field_id = \Acelle\Model\Field::findByUid($param['field_id'])->id;

                $condition->save();
            }

            // Log
            $segment->log('updated', $request->user());

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.segment.updated'));

            return redirect()->action('SegmentController@index', $list->uid);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $items = \Acelle\Model\Segment::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::denies('delete', $item)) {
                return;
            }
        }

        foreach ($items->get() as $item) {
            $item->delete();

            // Log
            $item->log('deleted', $request->user());
        }

        // Redirect to my lists page
         // Redirect to my lists page
        echo trans('messages.segments.deleted');
    }

    /**
     * Get sample option line.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function sample_condition(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);

        return view('segments._sample_condition', [
            'list' => $list,
        ]);
    }

    /**
     * Select box with list.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function selectBox(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);

        return view('segments._select_box', [
            'options' => $list->getSegmentSelectOptions(),
            'index' => $request->index,
        ]);
    }
}
