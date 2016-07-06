<?php namespace App\Services;

use App\Models\Organization\Organization;
use App\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\RedirectResponse;

/**
 * Class Verification
 * @package App\Services
 */
class Verification
{
    /**
     * @var User
     */
    protected $user;
    /**
     * @var Mailer
     */
    protected $mailer;
    /**
     * @var Organization
     */
    protected $organization;

    /**
     * Verification constructor.
     * @param Mailer       $mailer
     * @param User         $user
     * @param Organization $organization
     */
    public function __construct(Mailer $mailer, User $user, Organization $organization)
    {
        $this->mailer       = $mailer;
        $this->user         = $user;
        $this->organization = $organization;
    }

    /**
     * sends email to all users
     * @param $user User
     */
    public function sendVerificationEmail($user)
    {
        $user           = $this->generateVerificationCode($user);
        $method         = [
            1 => 'getAdminComponents',
            2 => 'getUserComponents',
            5 => 'getUserComponents',
            6 => 'getUserComponents',
            7 => 'getUserComponents'
        ];
        $data           = $user->toArray();
        $emailComponent = $this->{$method[$user->role_id]}($data);

        $this->mailer->send($emailComponent['view'], $data, $emailComponent['callback']);
    }

    /**
     * return admin verification components
     * @param $data
     * @return array
     */
    protected function getAdminComponents($data)
    {
        $callback = function ($message) use ($data) {
            $message->subject('Welcome to AidStream');
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($data['email']);
        };

        return ['view' => 'emails.admin', 'callback' => $callback];
    }

    /**
     * return user verification components
     * @param $data
     * @return array
     */
    protected function getUserComponents($data)
    {
        $callback = function ($message) use ($data) {
            $message->subject('Welcome to AidStream');
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($data['email']);
        };

        return ['view' => 'emails.user', 'callback' => $callback];
    }

    /**
     * generates verification code for user and returns user
     * @param User $user
     * @return User
     */
    protected function generateVerificationCode($user)
    {
        $user->verification_code       = hash_hmac('sha256', str_random(40), config('app.key'));
        $user->verification_created_at = date('Y-m-d H:i:s', time());
        $user->save();

        return $user;
    }

    /**
     * verifies user
     * @param $code
     * @return RedirectResponse
     */
    public function verifyUser($code)
    {
        $user = $this->user->where('verification_code', $code)->first();
        if (!$user) {
            $message = 'The verification code is invalid.';
        } elseif ($user->update(['verified' => true])) {
            $method = [
                1 => 'verifyAdmin',
                2 => 'verifyOrgUser',
                5 => 'verifyOrgUser',
                6 => 'verifyOrgUser',
                7 => 'verifyOrgUser'
            ];

            return $this->{$method[$user->role_id]}($user);
        } else {
            $message = 'Failed to verify your account.';
        }

        return redirect()->to('/auth/login')->withErrors([$message]);
    }

    /**
     * verifies admin
     * @param User $user
     * @return RedirectResponse
     */
    protected function verifyAdmin(User $user)
    {
        $users = $this->user->join('role', 'users.role_id', '=', 'role.id')
                            ->select(['*', 'users.id as id'])
                            ->where('org_id', $user->org_id)
                            ->whereNotNull('permissions')
                            ->orderBy('users.id', 'asc')
                            ->get();
        $this->sendVerificationEmailToUsers($users);
        $this->sendSecondaryVerificationEmail($user->organization);
        $message = view('verification.admin', compact('users', 'user'));

        return redirect()->to('/auth/login')->withVerificationMessage($message->__toString());
    }

    /**
     * verifies secondary user
     * @param $code
     * @return RedirectResponse
     */
    public function verifySecondary($code)
    {
        $organization                    = $this->organization->whereRaw("secondary_contact ->> 'verification_code' = ?", [$code])->first();
        $user                            = $organization->secondary_contact;
        $user['verified']                = true;
        $organization->secondary_contact = $user;
        $message                         = view('verification.secondary');
        $organization->save();

        return redirect()->to('/')->withVerificationMessage($message->__toString());
    }

    /**
     * verifies organization user
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function verifyOrgUser(User $user)
    {
        return redirect()->route('show-create-password', $user->verification_code);
    }

    /**
     * saves Registry Information
     * @param $code
     * @param $registryInfo
     * @return bool
     */
    public function saveRegistryInfo($code, $registryInfo)
    {
        return false;
    }

    /**
     * sends verification email to secondary contact
     * @param $organization
     */
    protected function sendSecondaryVerificationEmail($organization)
    {
        $secondary                            = $organization->secondary_contact;
        $secondary['verification_code']       = hash_hmac('sha256', str_random(40), config('app.key'));
        $secondary['verification_created_at'] = date('Y-m-d H:i:s', time());
        $secondary['verified']                = false;
        $organization->secondary_contact      = $secondary;
        $organization->save();

        $data             = $secondary;
        $data['admin']    = $organization->users->where('role_id', 1)->first()->toArray();
        $orgName          = $organization->orgData->name[0]['narrative'];
        $data['org_name'] = $orgName;
        $callback         = function ($message) use ($data) {
            $message->subject(sprintf('%s is now live on AidStream', $data['org_name']));
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($data['email']);
        };

        $this->mailer->send('emails.secondary', $data, $callback);
    }

    /**
     * sends verification email to organization users
     * @param $users
     */
    protected function sendVerificationEmailToUsers($users)
    {
        foreach ($users as $user) {
            $this->sendVerificationEmail($user);
        }
    }
}
