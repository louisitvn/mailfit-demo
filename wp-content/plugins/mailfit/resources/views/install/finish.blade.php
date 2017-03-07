@extends('layouts.install')

@section('title', trans('messages.finish'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('content')


        <h3 class="text-teal-800"><i class="icon-checkmark4"></i> Congratulations, you've successfully installed Acelle Email Marketing Application (AcelleMail)</h3>
            
        
        Now, you can go to your Admin Panel with link: <a class="text-semibold" href="{{ action('Admin\HomeController@index') }}">Dashboard</a>.
        <br />
        If you are having problems or suggestions, please visit <a class="text-semibold" href="http://mailfit.net" target="_blank">MailFit.net official website</a>.
        <br><br>

        Thank you for chosing AcelleMail.
        <div class="clearfix"><!-- --></div>      
<br />

@endsection
