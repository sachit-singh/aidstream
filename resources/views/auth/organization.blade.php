<div class="input-wrapper">
    <p>Please provide the information below about the organisation you want to create an account for on AidStream.</p>
</div>

<div class="input-wrapper">
    <div class="col-xs-12 col-md-12">
        {!! AsForm::text(['name' => 'organization[organization_name]', 'class' => 'organization_name', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6', 'html' => '<span class="availability-check hidden"></span>']) !!}
        {!! AsForm::text(['name' => 'organization[organization_name_abbr]', 'class' => 'organization_name_abbr', 'label' => 'Organization Name Abbreviation', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6', 'html' => '<span class="availability-check hidden"></span>']) !!}
    </div>
    <div class="col-xs-12 col-md-12">
        {!! AsForm::select(['name' => 'organization[organization_type]', 'class' => 'organization_type', 'data' => $orgType, 'required' => true , 'parent' => 'col-xs-12 col-sm-6 col-md-6', 'empty_value' => 'Select a Type']) !!}
    </div>
    <div class="col-xs-12 col-md-12">
        {!! AsForm::text(['name' => 'organization[organization_address]', 'class' => 'organization_address', 'label' => 'Address', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
        {!! AsForm::select(['name' => 'organization[country]', 'class' => 'country', 'data' => $countries, 'required' => true , 'parent' => 'col-xs-12 col-sm-6 col-md-6', 'empty_value' => 'Select a Country']) !!}
    </div>
    <div class="col-xs-12 col-md-12{{ $errors->get('organization.organization_identifier') ? ' has-error' : '' }}">
        {!! AsForm::select(['name' => 'organization[organization_registration_agency]', 'class' => 'organization_registration_agency', 'data' => $orgRegAgency, 'required' => true , 'parent' => 'col-xs-12 col-sm-6 col-md-6', 'empty_value' => 'Select an Agency']) !!}
        {!! AsForm::text(['name' => 'organization[registration_number]', 'class' => 'registration_number', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
        <p>
            <button type="button" class="btn-xs btn-link add_agency">Add Agency</button>
            {{ Form::hidden('organization[agencies]', ($agencies = getVal($regInfo, ['organization', 'agencies'], [])) ? $agencies : json_encode($orgRegAgency), ['class' => 'form-control agencies', 'id' => 'agencies', 'data-agency' => getVal($regInfo, ['organization', 'organization_registration_agency'])]) }}
            {{ Form::hidden('organization[new_agencies]', null, ['class' => 'form-control new_agencies', 'id' => 'organization[new_agencies]']) }}
            {{ Form::hidden('organization[agency_name]', null, ['class' => 'form-control agency_name', 'id' => 'organization[agency_name]']) }}
            {{ Form::hidden('organization[agency_website]', null, ['class' => 'form-control agency_website', 'id' => 'organization[agency_website]']) }}
        </p>
    </div>
    <div class="text-center">
        IATI Organizational Identifier: <span id="org_identifier">[Registration Agency]-[Registration Number]</span>
        {{ Form::hidden('organization[organization_identifier]', null, ['class' => 'form-control organization_identifier', 'id' => 'organization[organization_identifier]']) }}

        @foreach($errors->get('organization.organization_identifier') as $message)
            <div class="text-danger">{{ $message }}</div>
        @endforeach
    </div>
</div>

<div class="col-md-12 text-center">
    {{ Form::button('Continue Registration', ['class' => 'btn btn-primary btn-submit btn-register btn-tab', 'type' => 'button',  'data-tab-trigger' => '#tab-users']) }}
</div>
