<?php

$language = Cookie::get('language');
if (isset($language)) {
    App::setLocale($language);
}

$router->get(
    '/public/files/xml/{file}',
    function ($file) {
        return redirect('/files/xml/' . $file);
    }
);

$router->get('/', 'HomeController@index');
$router->get('home', 'HomeController@index');
$router->get('about', 'AboutController@index');
$router->get('who-is-using', 'WhoIsUsingController@index');
$router->get('who-is-using/{page}/{count}', 'WhoIsUsingController@listOrganization');
$router->get('admin/dashboard', 'SuperAdmin\OrganizationController@adminDashboard');
$router->resource('settings', 'Complete\SettingsController');

$router->put(
    'update-settings',
    [
        'as'   => 'update-settings',
        'uses' => 'Complete\SettingsController@updateSettings'
    ]
);

$router->post(
    'change-segmentation',
    [
        'as'   => 'change-segmentation',
        'uses' => 'Complete\SettingsController@changeSegmentation'
    ]
);

$router->post(
    'activity/{activity}/complete',
    [
        'as'   => 'activity.complete',
        'uses' => 'Complete\WorkflowController@complete'
    ]
);

$router->post(
    'activity/{activity}/verify',
    [
        'as'   => 'activity.verify',
        'uses' => 'Complete\WorkflowController@verify'
    ]
);

$router->post(
    'activity/{activity}/publish',
    [
        'as'   => 'activity.publish',
        'uses' => 'Complete\WorkflowController@publish'
    ]
);

$router->get('who-is-using/{organization_id}', 'WhoIsUsingController@getDataForOrganization');

$router->controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);

$router->post(
    'check-organization-user-identifier',
    [
        'as'   => 'check-organization-user-identifier',
        'uses' => 'Auth\AuthController@checkUserIdentifier'
    ]
);

$router->get('logs', 'LogViewerController@index');

$router->get(
    'show-logs',
    [
        'as'   => 'show-logs',
        'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'
    ]
);

$router->get(
    'admin/activity-log',
    [
        'SuperAdmin' => true,
        'as'         => 'admin.activity-log',
        'uses'       => 'Complete\AdminController@index'
    ]
);

$router->get(
    'admin/activity-log/organization/{orgId}',
    [
        'SuperAdmin' => true,
        'as'         => 'admin.activity-log.organization',
        'uses'       => 'Complete\AdminController@index'
    ]
);

$router->get(
    'admin/activity-log/{id}',
    [
        'SuperAdmin' => true,
        'as'         => 'admin.activity-log.view-data',
        'uses'       => 'Complete\AdminController@viewData'
    ]
);

$router->get(
    'organization-user/register',
    [
        'as'   => 'admin.register-user',
        'uses' => 'Complete\AdminController@create'
    ]
);

$router->get(
    'organization-user',
    [
        'as'   => 'admin.list-users',
        'uses' => 'Complete\AdminController@listUsers'
    ]
);

$router->post(
    'organization-user',
    [
        'as'   => 'admin.signup-user',
        'uses' => 'Complete\AdminController@store'
    ]
);

$router->get(
    'organization-user/view-profile/{id}',
    [
        'as'   => 'admin.view-profile',
        'uses' => 'Complete\AdminController@viewUserProfile'
    ]
);

$router->get(
    'organization-user/{id}/delete',
    [
        'as'   => 'admin.delete-user',
        'uses' => 'Complete\AdminController@deleteUser'
    ]
);

$router->get(
    'organization-user/reset-password/{id}',
    [
        'as'   => 'admin.reset-user-password',
        'uses' => 'Complete\AdminController@resetUserPassword'
    ]
);

$router->post
(
    'organization-user/update-password/{id}',
    [
        'as'   => 'admin.update-user-password',
        'uses' => 'Complete\AdminController@updateUserPassword'
    ]
);


$router->get
(
    'organization-user/edit-permission/{id}',
    [
        'as'   => 'admin.edit-user-permission',
        'uses' => 'Complete\AdminController@editUserPermission'
    ]
);

$router->post
(
    'organization-user/update-permission/{id}',
    [
        'as'   => 'admin.update-user-permission',
        'uses' => 'Complete\AdminController@updateUserPermission'
    ]
);

$router->resource('upgrade-version', 'Complete\UpgradeController');
$router->get(
    'documents',
    [
        'as'   => 'documents',
        'uses' => 'Complete\DocumentController@index'
    ]
);
$router->post(
    'document/upload',
    [
        'as'   => 'document.upload',
        'uses' => 'Complete\DocumentController@store'
    ]
);
$router->get(
    'document/list',
    [
        'as'   => 'document.list',
        'uses' => 'Complete\DocumentController@getDocuments'
    ]
);
$router->get(
    'document/{id}/delete',
    [
        'as'   => 'document.delete',
        'uses' => 'Complete\DocumentController@destroy'
    ]
);
$router->get(
    'validate-activity/{id}',
    [
        'as'   => 'validate-activity',
        'uses' => 'CompleteValidateController@validateActivity'
    ]
);
$router->get(
    'validate-activity/{id}/version/{version?}',
    [
        'as'   => 'validate-activity',
        'uses' => 'CompleteValidateController@validateActivity'
    ]
);
$router->get(
    'validate-organization/{id}',
    [
        'as'   => 'validate-organization',
        'uses' => 'CompleteValidateController@validateOrganization'
    ]
);
$router->get(
    'validate-organization/{id}/version/{version?}',
    [
        'as'   => 'validate-organization',
        'uses' => 'CompleteValidateController@validateOrganization'
    ]
);
$router->get(
    'admin/updateOrganizationIdForUserActivities',
    [
        'SuperAdmin' => true,
        'as'         => 'admin.updateOrganizationIdForUserActivities',
        'uses'       => 'Complete\AdminController@updateOrganizationIdForUserActivities'
    ]
);

$router->get(
    'user-logs',
    [
        'as'   => 'user-logs',
        'uses' => 'Complete\UserLogController@search'
    ]
);

$router->post(
    'user-logs',
    [
        'as'   => 'user-logs.filter',
        'uses' => 'Complete\UserLogController@search'
    ]
);

$router->get(
    'user-logs/viewDeletedData/{id}',
    [
        'as'   => 'user-logs.viewDeletedData',
        'uses' => 'Complete\UserLogController@viewDeletedData'
    ]
);

$router->get(
    'register',
    [
        'as'   => 'registration',
        'uses' => 'Auth\RegistrationController@showRegistrationForm'
    ]
);

$router->post(
    'register',
    [
        'as'   => 'registration.register',
        'uses' => 'Auth\RegistrationController@register'
    ]
);

$router->match(
    ['GET', 'POST'],
    'find-similar-organizations/{type?}',
    [
        'as'   => 'similar-organizations',
        'uses' => 'Auth\RegistrationController@showSimilarOrganizations'
    ]
);

$router->post(
    'similar-organizations',
    [
        'as'   => 'submit-similar-organization',
        'uses' => 'Auth\RegistrationController@submitSimilarOrganization'
    ]
);

$router->post(
    'check-org-identifier',
    [
        'as'   => 'check-org-identifier',
        'uses' => 'Auth\RegistrationController@checkOrgIdentifier'
    ]
);

$router->get(
    'similar-organizations/{orgName}',
    [
        'as'   => 'similar-org',
        'uses' => 'Auth\RegistrationController@listSimilarOrganizations'
    ]
);

$router->get(
    'contact/{template}',
    [
        'as'   => 'contact',
        'uses' => 'ContactController@showContactForm'
    ]
);

$router->post(
    'contact/{template}',
    [
        'as'   => 'contact',
        'uses' => 'ContactController@processEmail'
    ]
);

$router->get(
    'registration/organization',
    [
        'as'   => 'registration.organization',
        'uses' => 'Auth\RegistrationController@showOrgForm'
    ]
);

$router->post(
    'registration/save-organization',
    [
        'as'   => 'registration.save-organization',
        'uses' => 'Auth\RegistrationController@saveOrganization'
    ]
);

$router->get(
    'registration/users',
    [
        'as'   => 'registration.users',
        'uses' => 'Auth\RegistrationController@showUsersForm'
    ]
);

$router->post(
    'registration/complete',
    [
        'as'   => 'registration.complete',
        'uses' => 'Auth\RegistrationController@completeRegistration'
    ]
);

$router->get(
    'user/verification/{code}',
    [
        'as'   => 'user-verification',
        'uses' => 'Auth\VerificationController@verifyUser'
    ]
);

$router->get(
    'user/secondary-verification/{code}',
    [
        'as'   => 'secondary-verification',
        'uses' => 'Auth\VerificationController@verifySecondary'
    ]
);

$router->post(
    'settings/registry-info/{code}',
    [
        'as'   => 'save-registry-info',
        'uses' => 'Auth\VerificationController@saveRegistryInfo'
    ]
);

$router->get(
    'user/create-password/{code}',
    [
        'as'   => 'show-create-password',
        'uses' => 'Auth\PasswordController@showCreatePasswordForm'
    ]
);

$router->post(
    'user/create-password/{code}',
    [
        'as'   => 'create-password',
        'uses' => 'Auth\PasswordController@createPassword'
    ]
);

$router->resource('agency', 'AgencyController');
