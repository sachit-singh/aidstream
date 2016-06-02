@extends('settings.settings')
@section('panel-body')
    <div class="panel-body">
        <div class="create-form settings-form">
            <div id="default_values">
                {!! form_start($form) !!}
                {!!  form_end($form) !!}
            </div>
        </div>
    </div>
@stop
@section('foot')
    <script src="/js/userOnBoarding.js"></script>
    <script>
        $(window).load(function () {
            @if(session('first_login'))
                UserOnBoarding.settingsTour();
            @endif
            UserOnBoarding.validateDefaultValues();
        });
    </script>

@endsection
