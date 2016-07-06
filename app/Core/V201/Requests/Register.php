<?php namespace app\Core\V201\Requests;

use App\Http\Requests\Request;
use App\Models\Organization\Organization;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Core\V201\Traits\GetCodes;

/**
 * Class Register
 * @package app\Core\V201\Requests
 */
class Register extends Request
{
    use GetCodes;

    /**
     * Register constructor.
     */
    public function __construct()
    {
        Validator::extend(
            'code_list',
            function ($attribute, $value, $parameters, $validator) {
                $listName = $parameters[1];
                $listType = $parameters[0];
                $codeList = $this->getCodes($listName, $listType);

                return in_array($value, $codeList);
            }
        );

        Validator::extend(
            'unique_email',
            function ($attribute, $value, $parameters, $validator) {
                $userEmails      = User::where('email', $value)->count();
                $secondaryEmails = Organization::whereRaw("secondary_contact ->> 'email' = ?", [$value])->count();

                return !($userEmails || $secondaryEmails);
            }
        );

        Validator::extend(
            'unique_org_identifier',
            function ($attribute, $value, $parameters, $validator) {
                $table    = 'organizations';
                $column   = 'reporting_org';
                $jsonPath = '{0,reporting_organization_identifier}';
                $builder  = \DB::table($table)->whereRaw(sprintf("%s #>> '{%s}' = ?", $column, str_replace('.', ',', $jsonPath)), [$value]);
                $count    = $builder->count();

                return $count === 0;
            }
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $orgRules  = $this->getRulesForOrg($this->get('organization'));
        $userRules = $this->getRulesForUsers($this->get('users'));
        $rules     = array_merge($rules, $orgRules, $userRules);

        return $rules;
    }

    /**
     * Get the Validation Error message
     * @return array
     */
    public function messages()
    {
        $messages = [];

        $orgMessages  = $this->getMessagesForOrg($this->get('organization'));
        $userMessages = $this->getMessagesForUsers($this->get('users'));
        $messages     = array_merge($messages, $orgMessages, $userMessages);

        return $messages;
    }

    /**
     * @param $organization
     * @return array
     */
    protected function getRulesForOrg($organization)
    {
        $formBase    = 'organization';
        $regAgencies = implode(',', array_keys(json_decode($organization['agencies'], true)));
        $rules       = [];

        $rules[sprintf('%s.organization_name', $formBase)]                = 'required';
        $rules[sprintf('%s.organization_name_abbr', $formBase)]           = 'required|unique:organizations,user_identifier';
        $rules[sprintf('%s.organization_type', $formBase)]                = 'required|code_list:Organization,OrganizationType';
        $rules[sprintf('%s.organization_address', $formBase)]             = 'required';
        $rules[sprintf('%s.country', $formBase)]                          = 'required|code_list:Organization,Country';
        $rules[sprintf('%s.organization_registration_agency', $formBase)] = 'required|in:' . $regAgencies;
        $rules[sprintf('%s.registration_number', $formBase)]              = 'required|alpha_num';
        $rules[sprintf('%s.organization_identifier', $formBase)]          = 'required|unique_org_identifier';

        return $rules;
    }

    /**
     * @param $organization
     * @return array
     */
    protected function getMessagesForOrg($organization)
    {
        $formBase = 'organization';
        $messages = [];

        $messages[sprintf('%s.organization_name.required', $formBase)]                    = 'Organization Name is required.';
        $messages[sprintf('%s.organization_name_abbr.required', $formBase)]               = 'Organization Name Abbreviation is required.';
        $messages[sprintf('%s.organization_name_abbr.unique', $formBase)]                 = 'Organization Name Abbreviation has already been taken.';
        $messages[sprintf('%s.organization_type.required', $formBase)]                    = 'Organization Type is required.';
        $messages[sprintf('%s.organization_type.code_list', $formBase)]                   = 'Organization Type is not valid.';
        $messages[sprintf('%s.organization_address.required', $formBase)]                 = 'Address is required.';
        $messages[sprintf('%s.country.required', $formBase)]                              = 'Country is required.';
        $messages[sprintf('%s.country.code_list', $formBase)]                             = 'Country is not valid.';
        $messages[sprintf('%s.organization_registration_agency.required', $formBase)]     = 'Organisation Registration Agency is required.';
        $messages[sprintf('%s.organization_registration_agency.reg_agency', $formBase)]   = 'Organisation Registration Agency is not valid.';
        $messages[sprintf('%s.registration_number.required', $formBase)]                  = 'Registration Number is required.';
        $messages[sprintf('%s.registration_number.alpha_num', $formBase)]                 = 'Registration Number may only contain letters and numbers.';
        $messages[sprintf('%s.organization_identifier.required', $formBase)]              = 'IATI Organizational Identifier is required.';
        $messages[sprintf('%s.organization_identifier.unique_org_identifier', $formBase)] = 'IATI Organizational Identifier is has already been taken.';

        return $messages;
    }

    /**
     * @param $users
     * @return array
     */
    protected function getRulesForUsers($users)
    {
        $formBase = 'users';
        $rules    = [];

        $rules[sprintf('%s.first_name', $formBase)]        = 'required';
        $rules[sprintf('%s.last_name', $formBase)]         = 'required';
        $rules[sprintf('%s.email', $formBase)]             = 'required|email|unique_email';
        $rules[sprintf('%s.password', $formBase)]          = 'required|min:6';
        $rules[sprintf('%s.confirm_password', $formBase)]  = sprintf('required|min:6|same:%s.password', $formBase);
        $rules[sprintf('%s.secondary_contact', $formBase)] = 'required|email|unique_email';

        $rules = array_merge($rules, $this->getRulesForOrgUsers(getVal($users, ['user'], [])));

        return $rules;
    }

    /**
     * @param $users
     * @return array
     */
    protected function getMessagesForUsers($users)
    {
        $formBase = 'users';
        $messages = [];

        $messages[sprintf('%s.first_name.required', $formBase)]            = 'First Name is required.';
        $messages[sprintf('%s.last_name.required', $formBase)]             = 'Last Name is required.';
        $messages[sprintf('%s.email.required', $formBase)]                 = 'Email is required.';
        $messages[sprintf('%s.email.email', $formBase)]                    = 'Email is not valid.';
        $messages[sprintf('%s.email.unique_email', $formBase)]             = 'Email has already been taken.';
        $messages[sprintf('%s.password.required', $formBase)]              = 'Password is required.';
        $messages[sprintf('%s.password.min', $formBase)]                   = 'Password must be at least 6 characters.';
        $messages[sprintf('%s.confirm_password.required', $formBase)]      = 'Confirm Password is required.';
        $messages[sprintf('%s.confirm_password.min', $formBase)]           = 'Confirm Password must be at least 6 characters.';
        $messages[sprintf('%s.confirm_password.same', $formBase)]          = 'Passwords doesn\'t match.';
        $messages[sprintf('%s.secondary_contact.required', $formBase)]     = 'Secondary Contact is required.';
        $messages[sprintf('%s.secondary_contact.email', $formBase)]        = 'Secondary Contact Email is not valid.';
        $messages[sprintf('%s.secondary_contact.unique_email', $formBase)] = 'Secondary Contact Email has already been taken.';

        $messages = array_merge($messages, $this->getMessagesForOrgUsers(getVal($users, ['user'], [])));

        return $messages;
    }

    /**
     * @param $users
     * @return array
     */
    protected function getRulesForOrgUsers($users)
    {
        $users = (array) $users;
        $rules = [];

        $dbRoles = \DB::table('role')->select('id')->whereNotNull('permissions')->get();
        $roles   = [];
        foreach ($dbRoles as $role) {
            $roles[] = $role->id;
        }
        $roles = implode(',', $roles);

        foreach ($users as $userIndex => $user) {
            $formBase                                       = sprintf('users.user.%s', $userIndex);
            $rules[sprintf('%s.login_username', $formBase)] = 'required|unique:users,username';
            $rules[sprintf('%s.email', $formBase)]          = 'required|email|unique_email';
            $rules[sprintf('%s.first_name', $formBase)]     = 'required';
            $rules[sprintf('%s.last_name', $formBase)]      = 'required';
            $rules[sprintf('%s.role', $formBase)]           = 'required|in:' . $roles;
        }

        return $rules;
    }

    /**
     * @param $users
     * @return array
     */
    protected function getMessagesForOrgUsers($users)
    {
        $users    = (array) $users;
        $messages = [];

        foreach ($users as $userIndex => $user) {
            $formBase                                                   = sprintf('users.user.%s', $userIndex);
            $messages[sprintf('%s.login_username.required', $formBase)] = 'Username is required.';
            $messages[sprintf('%s.login_username.unique', $formBase)]   = 'Username has already been taken.';
            $messages[sprintf('%s.email.required', $formBase)]          = 'Email is required.';
            $messages[sprintf('%s.email.email', $formBase)]             = 'Email is not valid.';
            $messages[sprintf('%s.email.unique_email', $formBase)]      = 'Email has already been taken.';
            $messages[sprintf('%s.first_name.required', $formBase)]     = 'First Name is required.';
            $messages[sprintf('%s.last_name.required', $formBase)]      = 'Last Name is required.';
            $messages[sprintf('%s.role.required', $formBase)]           = 'Role is required.';
            $messages[sprintf('%s.role.in', $formBase)]                 = 'Role is not valid.';
        }

        return $messages;
    }
}
