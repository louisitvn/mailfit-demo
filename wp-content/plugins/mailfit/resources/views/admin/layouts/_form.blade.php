
							@include('helpers.form_control', ['class' => ($layout->type == 'page' ? 'full-editor' : 'email-editor'), 'required' => true, 'type' => 'textarea', 'name' => 'content', 'value' => $layout->content, 'rules' => ['content' => 'required']])
							
							<div class="text-right">
                                <button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
                                <a href="{{ action('Admin\LayoutController@index') }}" class="btn bg-grey-800"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
                            </div>