@extends('settings.settings')
@section('panel-body')
    <div class="panel-body">
        <div class="create-form settings-form">
            <div class="settings-checkall-wrapper">
                <h2>Activity elements checklist</h2>
                <p>Please check the elements you want to add to your activities. The greyed out checkboxes are required to be filled out in AidStream.</p>
                <hr />
                <div class="form-group">
                    <label><input type="checkbox" class="checkAll"/><span
                                class="check-text">Check All</span></label>
                </div>
            </div>
            {!! form_start($form) !!}
            {!!  form_end($form) !!}
        </div>
    </div>
@stop
