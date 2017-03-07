<div class="col-md-10">
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="row ">
                    <div class="col-md-3 checkboxes-with-check-all-first">
                        @include('helpers.form_control', [
                            'type' => 'checkboxes',
                            'name' => 'categories[]',
                            'class' => '',
                            'label' => trans('messages.choose_wordpress_categories'),
                            'value' => null !== $first_event->getDataValue('categories') ? implode(",", $first_event->getDataValue('categories')) : 'all',
                            'options' => Acelle\Library\Tool::getWordPressCategorySelectOptions(),
                            'rules' => []
                        ])
                    </div>
                    <div class="col-md-1">
                        <h4 class="text-muted2">{{ trans('messages.and') }}</h4>
                    </div>
                    <div class="col-md-3 checkboxes-with-check-all-first">
                        @include('helpers.form_control', [
                            'type' => 'checkboxes',
                            'name' => 'tags[]',
                            'class' => '',
                            'label' => trans('messages.choose_wordpress_tags'),
                            'value' => null !== $first_event->getDataValue('tags') ? implode(",", $first_event->getDataValue('tags')) : 'all',
                            'options' => Acelle\Library\Tool::getWordPressTagSelectOptions(),
                            'rules' => []
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>