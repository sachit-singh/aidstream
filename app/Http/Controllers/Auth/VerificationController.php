<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RequestManager\Password;
use App\Services\Verification;
use Illuminate\Http\RedirectResponse;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Auth
 */
class VerificationController extends Controller
{
    /**
     * @var Verification
     */
    protected $verificationManager;

    /**
     * VerificationController constructor.
     * @param Verification $verificationManager
     */
    public function __construct(Verification $verificationManager)
    {
        $this->verificationManager = $verificationManager;
    }

    /**
     * verifies user
     * @param $code
     * @return RedirectResponse
     */
    public function verifyUser($code)
    {
        return $this->verificationManager->verifyUser($code);
    }

    /**
     * verifies secondary
     * @param $code
     * @return RedirectResponse
     */
    public function verifySecondary($code)
    {
        return $this->verificationManager->verifySecondary($code);
    }

    /**
     * saves registry info
     * @param $code
     * @return RedirectResponse
     */
    public function saveRegistryInfo($code)
    {
        $registryInfo = request()->all();
        if ($this->verificationManager->saveRegistryInfo($code, $registryInfo)) {
            return redirect()->to('/auth/login')->withMessage('Registry Info Saved Successfully.');
        } else {
            return redirect()->to('/auth/login')->withErrors(['Failed to save Registry Info.']);
        }
    }
}
