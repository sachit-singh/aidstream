@extends('settings.settings')

@section('panel-body')
    <div class="panel-body">
        <div class="create-form settings-form">
            {{ Form::model($organization,['method'=>'POST', 'route' => 'organization-information.update','files' => true]) }}
            {!! form_rest($form) !!}
            <div class="col-md-12 col-xs-12">
                {!! AsForm::text(['name'=>'user_identifier', 'required' => true,'label'=>'Organisation Name Abbreviation','parent'=>'col-xs-12 col-sm-6 col-md-6']) !!}
                {!! AsForm::select(['name'=>'organization_type','data' => $organizationTypes, 'value' => getVal($organization->reporting_org, [ 0 ,'reporting_organization_type']),'empty_value' => 'Select one of the following options' ,'required' => true,'parent'=>'col-xs-12 col-sm-6 col-md-6']) !!}
            </div>
            <div class="col-md-12 col-xs-12">
                {!! AsForm::text(['name' => 'address','parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
                {!! AsForm::select(['name' => 'country','data' => $countries,'empty_value' => 'Select one of the following options','required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6','id' => 'country']) !!}
            </div>
            <h2>IATI Organisational Identifier</h2>
            <div class="col-md-12 col-xs-12">
                {!! AsForm::select(['name' => 'registration_agency', 'data' => $registrationAgency,'required' => true, 'label' => 'Organisation Registration Agency','parent' => 'col-xs-12 col-sm-6 col-md-6', 'id' => 'registration_agency', 'empty_value' => 'Select an Agency', 'attr' => ['data-agency' => json_encode($registrationAgency)]]) !!}
                {!! AsForm::text(['name' => 'registration_number', 'required'=> true, 'label' => 'Organisation Registration Number','parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
            </div>
            <div class="col-md-12 col-xs-12">
                {!! AsForm::text(['name'=>'organization_url' , 'label' => 'Organisation Website URL', 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
                {!! AsForm::text(['name'=>'twitter' , 'label' => 'Organisation Twitter Handler', 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
            </div>
            <div class="col-md-12 col-xs-12">
                {!! AsForm::text(['name' => 'telephone', 'label' => 'Organisation Telephone Number', 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
                <div class="description col-xs-12 col-sm-6 col-md-6">Please insert a valid twitter username. Example: '@oxfam ' or 'oxfam'</div>
            </div>
            <div class="col-md-6 col-xs-12">
                {{ Form::label(null,'Organisation Logo') }}
                {{ Form::file('organization_logo',['class'=>'form-control','id' => 'picture']) }}
            </div>
            <div class="col-md-6 col-xs-12">
                @if($organization->logo)
                    <img src="{{$organization->logo_url}}" height="150" width="150" alt="{{$organization->logo}}" id="selected_picture" style="border:1px solid black"/>
                @else
                    <img src="" height="150" width="150" alt="Uploaded Image" id="selected_picture" style="'border:1px solid black'" style="border:1px solid black"/>
                @endif
            </div>
            <div class="col-sm-4 col-xs-6">
                {{ Form::submit('Save',['class' => 'btn btn-primary form-control']) }}
            </div>
        </div>
        {{ Form::close() }}
        <div class="collection-container hidden"
             data-prototype="{{ form_row($form->narrative->prototype()) }}"></div>
    </div>
    </div>
@endsection
@section('foot')
    <script src="{{ url('js/chunk.js') }}"></script>
    <script>
        var agencies = JSON.parse($('#registration_agency').attr('data-agency'));
        $('document').ready(function () {
            Chunk.displayPicture();
            Chunk.changeCountry();
        });
    </script>
@endsection
