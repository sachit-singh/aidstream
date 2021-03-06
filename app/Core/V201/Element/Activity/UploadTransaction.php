<?php namespace App\Core\V201\Element\Activity;

/**
 * Class UploadTransaction
 * @package App\Core\V201\Element\Activity
 */
class UploadTransaction
{
    /**
     * @return transaction form
     */
    public function getForm()
    {
        return 'App\Core\V201\Forms\Activity\Transactions\UploadTransaction';
    }

    /**
     * @return transaction repository
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\UploadTransaction');
    }
}
