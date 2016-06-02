<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Services\Contact as ContactManager;
use App\Services\RequestManager\Contact;

class ContactController extends Controller
{
    /**
     * @var ContactManager
     */
    protected $contactManager;

    /**
     * ContactController constructor.
     * @param ContactManager $contactManager
     */
    public function __construct(ContactManager $contactManager)
    {
        $this->contactManager = $contactManager;
    }

    public function showContactForm()
    {
        return view('auth.contact');
    }

    public function notMyOrganization(Contact $request)
    {
        if ($this->contactManager->notMyOrganization($request->all())) {
            return redirect()->to('/')->withMessage('Your query has been submitted.');
        } else {
            return redirect()->back()->withInput()->withErrorMessage('Failed to submit your query. Please try again.');
        }
    }

    public function needNewUser(Contact $request)
    {
        if ($this->contactManager->needNewUser($request->all())) {
            return redirect()->to('/')->withMessage('Your query has been submitted.');
        } else {
            return redirect()->back()->withInput()->withErrorMessage('Failed to submit your query. Please try again.');
        }
    }
}
