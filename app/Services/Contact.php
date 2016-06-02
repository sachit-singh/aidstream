<?php namespace App\Services;

use Exception;
use Illuminate\Contracts\Logging\Log as Logger;
use Illuminate\Contracts\Mail\Mailer;

class Contact
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Mailer
     */
    protected $mailer;

    protected $methods = [
        'not-my-organization' => 'getNotMyOrg',
        'need-new-user'       => 'getNeedNewUser'
    ];

    /**
     * Contact constructor.
     * @param Logger $logger
     * @param Mailer $mailer
     */
    public function __construct(Logger $logger, Mailer $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function processEmail($data, $template)
    {
        try {
            $this->{$this->methods[$template]}($data);
            $this->sendEmail($data);

            return true;
        } catch (Exception $exception) {
            $this->logger->error($exception, ['data' => $data]);
        }

        return false;
    }

    protected function sendEmail($data)
    {
        $callback = function ($message) use ($data) {
            $message->subject($data['subject']);
            $message->from($data['email'], $data['full_name']);
            $message->to($data['emailTo']);
        };
        $this->mailer->raw($data['message'], $callback);
    }

    protected function getNotMyOrg(&$data)
    {
        $data['emailTo'] = env('MAIL_ADDRESS');
        $data['subject'] = 'Not My Organisation';
    }

    protected function getNeedNewUser(&$data)
    {
        $data['emailTo'] = session()->pull('admin_email');
        $data['subject'] = 'New User Account Needed';
    }
}