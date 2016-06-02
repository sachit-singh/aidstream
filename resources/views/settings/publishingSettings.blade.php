@extends('settings.settings')
@section('panel-body')
    <div class="panel-body">
        <div class="panel-content-heading">Registry Information</div>
        <div class="create-form settings-form">
            {!! form_start($form) !!}
            {!!  form_end($form) !!}
        </div>
    </div>
@endsection
@section('foot')
    <script src="{{url('js/chunk.js')}}"></script>
    <script>
            Chunk.verifyPublisherAndApi();

    </script>
@endsection
