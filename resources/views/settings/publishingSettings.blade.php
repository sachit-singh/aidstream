@extends('settings.settings')
@section('panel-body')
    <div class="panel-body">
        <div class="panel-content-heading">Registry Information</div>
        <div class="create-form settings-form">
            {!! form_start($form) !!}
            <div id="publishing_info1">
                {!! form_until($form,'verify') !!}
            </div>
            <div id="publishing_info2">
                {!!  form_until($form,'publishing') !!}
            </div>
            <div id="publishing_info3">
                {!! form_until($form,'publish_files') !!}
            </div>
            {!!  form_end($form) !!}
        </div>
    </div>
@endsection
@section('foot')
    <script src="{{url('js/chunk.js')}}"></script>
    <script src="{{url('js/userOnBoarding.js')}}"></script>
    <script>
        $(window).load(function () {
            Chunk.verifyPublisherAndApi();
            @if(session('first_login'))
                UserOnBoarding.settingsTour();
            @endif
            UserOnBoarding.validatePublishingInfo();
        });
    </script>
@endsection
