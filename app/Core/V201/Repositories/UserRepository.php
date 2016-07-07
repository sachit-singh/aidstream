<?php namespace App\Core\V201\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Class UserRepository
 * @package App\Core\V201\Repositories
 */
class UserRepository
{
    protected $current_user;

    protected $user;

    public function __construct(User $user)
    {
        $this->current_user = Auth::user();
        $this->user         = $user;
    }

    /** Update profile of the user
     * @param $input
     * @param $organization_identifier
     */
    public function updateUserProfile($input, $organization_identifier)
    {
        $user = $this->current_user;

        if (!Auth::user()->isAdmin()) {
            $user->username = $organization_identifier . '_' . $input['username'];
        }

        $user->first_name   = $input['first_name'];
        $user->last_name    = $input['last_name'];
        $user->email        = $input['email'];
        $timeZone           = explode(' : ', $input['time_zone']);
        $user->time_zone_id = $timeZone[0];
        $user->time_zone    = $timeZone[1];

        $file  = Input::file('profile_picture');
        $orgId = session('org_id');

        if ($file) {
            $fileUrl  = url('files/logos/' . $orgId . '.' . $file->getClientOriginalExtension());
            $fileName = $orgId . '.' . $file->getClientOriginalExtension();
            $image    = Image::make(File::get($file))->resize(150, 150)->encode();
            Storage::put('logos/' . $fileName, $image);
            $user->profile_url     = $fileUrl;
            $user->profile_picture = $fileName;
        }

        $user->save();

        if (array_key_exists('secondary_email', $input)) {
            $this->updateSecondaryContactInfo($input, $orgId);
        }
    }

    /** update secondary contact of the organization.
     * @param $input
     * @param $orgId
     */
    public function updateSecondaryContactInfo($input, $orgId)
    {
        $user = $this->user->where('role_id', 7)->where('org_id', $orgId)->first();

        if ($user) {
            $user->first_name = $input['secondary_first_name'];
            $user->last_name  = $input['secondary_last_name'];
            $user->email      = $input['secondary_email'];
            $user->role_id    = 7;
            $user->org_id     = $orgId;
            $user->username   = $input['secondary_email'];
            $user->password   = "";

            $user->save();
        } else {
            User::create(
                [
                    'first_name' => $input['secondary_first_name'],
                    'last_name'  => $input['secondary_last_name'],
                    'email'      => $input['secondary_email'],
                    'role_id'    => 7,
                    'username'   => $input['secondary_email'],
                    'password'   => '',
                    'org_id'     => $orgId
                ]
            );
        }
    }

    /** returns secondary contacts of the organization.
     * @return mixed
     */
    public function getSecondaryContactInfo()
    {
        return $this->user->where('org_id', session('org_id'))->where('role_id', 7)->first();
    }

    /** returns details of the given user.
     * @param $userId
     * @return mixed
     */
    public function getUser($userId)
    {
        return $this->user->findOrFail($userId);
    }

    /** returns all the users of the organization.
     * @return mixed
     */
    public function getAllUsersOfOrganization()
    {
        return $this->user->where('org_id', session('org_id'))->get();
    }

    /** updates username of the organization.
     * @param $old_user_identifier
     * @param $new_user_identifier
     */
    public function updateUsername($old_user_identifier, $new_user_identifier)
    {
        $users = $this->getAllUsersOfOrganization();
        foreach ($users as $user) {
            if ($user->role_id != 7) {
                $old_username   = $user->username;
                $nameOnly       = substr($old_username, strlen($old_user_identifier) + 1);
                $user->username = $new_user_identifier . '_' . $nameOnly;
                $user->save();
            }
        }
    }
}