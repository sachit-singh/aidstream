<?php namespace App\Services;


use App\Core\V201\Repositories\UserRepository;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\Facades\Auth;
use Psr\Log\LoggerInterface;

class UserManager
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Log
     */
    protected $dbLogger;

    function __construct(UserRepository $userRepository, LoggerInterface $logger, Log $dbLogger)
    {
        $this->userRepository = $userRepository;
        $this->logger         = $logger;
        $this->dbLogger       = $dbLogger;
    }

    public function updateUserProfile($input, $organization_identifier)
    {
        try {
            $this->userRepository->updateUserProfile($input, $organization_identifier);
            $this->logger->info('Profile Updated Successfully.');
            $this->dbLogger->activity(
                "activity.settings_updated",
                [
                    'organization'    => Auth::user()->organization->name,
                    'organization_id' => Auth::user()->organization->id
                ]
            );

            return true;
        } catch (Exception $e) {
            $this->logger->error($e, ['settings' => $input]);
        }

        return false;
    }

    public function getUser($userId)
    {
        return $this->userRepository->getUser($userId);
    }

    public function getUserDetails($userId)
    {
        $secondaryContact = $this->getSecondaryContactInfo();
        $user             = $this->getUser($userId);

        $userDetails = [
            'id'              => $user->id,
            'username'        => $user->username,
            'first_name'      => $user->first_name,
            'last_name'       => $user->last_name,
            'email'           => $user->email,
            'permission'      => '',
            'time_zone_id'    => $user->time_zone_id,
            'time_zone'       => $user->time_zone,
            'profile_picture' => $user->profile_picture,
            'profile_url'     => $user->profile_url
        ];

        if ($secondaryContact && isUserOrAdmin(Auth::user())) {
            $userDetails = array_merge(
                $userDetails,
                [
                    'secondary_email'      => $secondaryContact->email,
                    'secondary_first_name' => $secondaryContact->first_name,
                    'secondary_last_name'  => $secondaryContact->last_name
                ]
            );
        }

        return $userDetails;

    }

    /** returns secondary contacts of the organization.
     * @return mixed
     */
    public function getSecondaryContactInfo()
    {
        return $this->userRepository->getSecondaryContactInfo();

    }

    /** returns all the users of the organization
     * @return mixed
     */
    public function getAllUsersOfOrganization()
    {
        return $this->userRepository->getAllUsersOfOrganization();
    }

}