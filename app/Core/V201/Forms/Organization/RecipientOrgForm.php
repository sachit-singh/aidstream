<?php namespace App\Core\V201\Forms\Organization;

use Kris\LaravelFormBuilder\Form;

class RecipientOrgForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('Ref', 'text');
    }
}
