@extends('settings.settings')
@section('panel-body')
    <div class="panel-body">

        <div class="alert alert-success">The usernames has been changed.</div>
        <ol>
            @foreach($users as $user)
                @if($user->role_id != 7)
                    <li>{{$user->first_name}} {{ $user->last_name }} {{$user->username}}</li>
                @endif
            @endforeach
        </ol>
        <p>Would you like us to notify the users through email about this change?</p>

        <div class="col-xs-12 col-md-12">
            <div class="col-md-4 col-xs-6">
                <button class="btn btn-primary">
                    <a href="{{route('organization-information.notify-user')}}" class="btn btn-default">Notify</a>
                </button>
            </div>
            <div class="col-md-4 col-xs-6">
                <button class="btn btn-primary">
                    <a href="{{route('settings')}}" class="btn btn-default">No. I will tell them myself</a>
                </button>
            </div>
        </div>
    </div>
@endsection