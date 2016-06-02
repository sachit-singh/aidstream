@extends('settings.settings')

@section('title', 'Register User')

@section('panel-body')
    <div class="panel-content-heading">User Information</div>
    <div class="create-form create-user-form">
        {{ Form::model(old(),['route' => 'admin.signup-user', 'method' => 'POST']) }}
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    {{ Form::label(null,'First Name*') }}
                    {{ Form::text('first_name',null,['class' => 'form-control','required']) }}
                </div>
                <div class="col-xs-12 col-sm-6">
                    {{ Form::label(null,'Last Name*') }}
                    {{ Form::text('last_name',null,['class' => 'form-control','required']) }}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    {{ Form::label(null,'E-Mail Address*') }}
                    {{ FOrm::email('email',null,['class' => 'form-control','required']) }}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 ">
                    {{ Form::label(null,'Username*') }}
                    <span>{{$organizationIdentifier}}</span>
                    {{ Form::text('username',null,['class'=>'form-control','id'=>'username']) }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                {{ Form::label(null,'Password*') }}
                {{ Form::password('password'.null,['class' => 'form-control','required']) }}
            </div>
            <div class="col-xs-12 col-sm-6">
                {{ Form::label(null,'Confirm Password*') }}
                {{ Form::password('password_confirmation',null,['class' => 'form-control']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                {{ Form::label(null,'Permission Level*') }}
                {{ Form::select('permissions',['Viewer','Editor','Publisher','Administrator'],['class'=>'form-control']) }}
            </div>
        </div>
        {{ Form::submit('Create',['class'=>'btn btn-primary btn-form btn-submit']) }}
        <a href="{{route('admin.list-users')}}" class="btn btn-cancel">
            Cancel
        </a>
        {{ Form::close() }}
    </div>
@endsection
