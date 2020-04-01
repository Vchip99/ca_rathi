@extends('admin.master')
@section('content')
    @if(Session::has('login_message'))
        <div class="alert alert-success" id="message">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('login_message') }}
        </div>
        {{ Session::forget('login_message') }}
    @endif

@stop