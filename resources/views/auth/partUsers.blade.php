<div class="user-block clearfix" style="background-color: #EBF8FF; margin-bottom: 10px;">
    <a href="#delete" class="delete pull-right">remove</a>
    <div class="col-xs-12 col-md-12">
        {!! AsForm::username(['name' => 'users[user][' . $userIndex . '][username]', 'hiddenName' => 'users[user][' . $userIndex . '][login_username]', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6', 'class' => 'username']) !!}
        {!! AsForm::email(['name' => 'users[user][' . $userIndex . '][email]', 'class' => 'email', 'label' => 'E-mail Address', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
    </div>
    <div class="col-xs-12 col-md-12">
        {!! AsForm::text(['name' => 'users[user][' . $userIndex . '][first_name]', 'class' => 'first_name', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
        {!! AsForm::text(['name' => 'users[user][' . $userIndex . '][last_name]', 'class' => 'last_name', 'required' => true, 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
    </div>
    <div class="col-xs-12 col-md-12">
        {!! AsForm::select(['name' => 'users[user][' . $userIndex . '][role]', 'class' => 'role', 'label' => 'Permission Role', 'data' => $roles, 'empty_value' => 'Select a Role', 'required' => true , 'parent' => 'col-xs-12 col-sm-6 col-md-6']) !!}
    </div>
</div>
