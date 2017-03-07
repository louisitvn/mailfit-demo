@extends('layouts.page')

@section('title', trans('messages.' . $page->layout->alias . '.real'))

@section('content')
                {!! $page->content !!}
@endsection