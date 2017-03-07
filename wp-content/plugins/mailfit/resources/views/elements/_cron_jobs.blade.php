<h3 class="text-teal-800"><i class="icon-puzzle2"></i> {{ trans('messages.setting_up_background_job') }}</h3>

@if(!$valid || isset($show_all))
    @include('helpers.form_control', [
        'type' => 'radio',
        'name' => 'queue_driver',
        'class' => '',
        'label' => trans('messages.choose_background_job_method'),
        'value' => $queue_driver,
        'options' => \Acelle\Library\Tool::availableSystemBackgroundMethodSelectOptions(),
        'rules' => [['queue_driver' => 'required']]
    ])
@endif

<div class="database-config-box">
    @if(!$valid || isset($show_all))
        @include('helpers.form_control', [
            'type' => 'radio',
            'name' => 'php_bin_path',
            'class' => '',
            'label' => trans('messages.find_php_bin_path'),
            'value' => $php_bin_path,
            'options' => \Acelle\Library\Tool::phpPathsSelectOptions($php_paths),
            'rules' => [['php_bin_path' => 'required']]
        ])
        
        <div class="php_bin_path_value_box">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'php_bin_path_value',
                'class' => '',
                'placeholder' => 'Example: /usr/local/bin/php',
                'label' => trans('messages.enter_php_bin_path'),
                'value' => $php_bin_path_value,
                'rules' => [['php_bin_path_value' => 'required']]
            ])    
        </div>
        
        @if ($errors->has('php_bin_path_invalid'))
            <div class="alert alert-danger">
                <?php $check = \Acelle\Library\Tool::checkPHPBinPath($php_bin_path_value); ?>
                @if ($check == '')
                    {{ trans('messages.this_is_not_executable_php_bin_path') }}
                @else
                    {!! $check !!}
                @endif
            </div>
        @endif
    @endif
    
    @if(!exec_enabled())
        <div class="alert alert-warning">
            {{ trans('messages.please_enable_php_exec_for_cronjob_check') }}
        </div>
    @endif
    
    @if($valid || isset($show_all))
        <hr>
    
        <label class="text-bold">{!! trans('messages.cron_jobs_guide') !!} </label>
        <pre style="font-size: 16px;background:#f5f5f5">* * * * * <span class="current_path_value">{!! $php_bin_path_value !!}</span> -q {{ base_path() }}/artisan schedule:run 2&gt;&amp;1    </pre>
    @endif
</div>
    
<script>
    $(document).ready(function() {
        // pickadate mask
        $(document).on('change', 'input[name="php_bin_path"]', function() {      
            var value = $(this).val();
            var old = $('.current_path_value').attr('old');
            
            if(value !== 'manual') {
                $('.current_path_value').html(value);
                $('input[name="php_bin_path_value"]').val(value);
            }
            
            if(value === 'manual') {
                $('.php_bin_path_value_box').show();
                $('input[name="php_bin_path_value"]').trigger('change');
            } else {
                $('.php_bin_path_value_box').hide();
            }
        });
        $('input[name="php_bin_path"]:checked').trigger('change');
        
        // pickadate mask
        $(document).on('keyup change', 'input[name="php_bin_path_value"]', function() {      
            var value = $(this).val();
            
            if(value !== '') {
                $('.current_path_value').html(value);
            } else {
                $('.current_path_value').html('{PHP_BIN_PATH}');
            }
        });
        $('input[name="php_bin_path_value"]').trigger('change');
        
        // pickadate mask
        $(document).on('change', 'input[name="queue_driver"]', function() {      
            var value = $(this).val();
            
            if(value === 'database') {
                $('.database-config-box').show();
            } else {
                $('.database-config-box').hide();
            }
        });
        $('input[name="queue_driver"]:checked').trigger('change');
    });
</script>
