@extends('layouts.frontend')

@section('title', $campaign->name)
	
@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    
    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('page_header')
	
			@include("campaigns._header")

@endsection

@section('content')
                
            @include("campaigns._menu")
			
			@include("campaigns._info")
            

            
            @include("campaigns._chart")
                

            
			@include("campaigns._open_click_rate")
			
			@include("campaigns._count_boxes")
            

            
            @include("campaigns._24h_chart")
            
            

			
			@include("campaigns._top_link")
            

			
			@include("campaigns._most_click_country")
            

			
			@include("campaigns._most_open_country")
				

			
			@include("campaigns._most_open_location")
			

@endsection
