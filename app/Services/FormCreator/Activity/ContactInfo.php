<?php namespace App\Services\FormCreator\Activity;

use App\Core\Version;
use Kris\LaravelFormBuilder\FormBuilder;

/**
 * Class ContactInfo
 * @package App\Services\FormCreator\Activity
 */
class ContactInfo
{

    protected $formBuilder;
    protected $version;
    protected $formPath;

    /**
     * @param FormBuilder $formBuilder
     * @param Version     $version
     */
    function __construct(FormBuilder $formBuilder, Version $version)
    {
        $this->formBuilder = $formBuilder;
        $this->version     = $version;
        $this->formPath    = $this->version->getActivityElement()->getContactInfo()->getForm();
    }

    /**
     * @param array $data
     * @param       $activityId
     * @return $this
     * return activity contact info edit form.
     */
    public function editForm($data, $activityId)
    {
        $model['contact_info'] = $data;

        return $this->formBuilder->create(
            $this->formPath,
            [
                'method' => 'PUT',
                'model'  => $model,
                'url'    => route('activity.contact-info.update', [$activityId, 0])
            ]
        )->add('Save', 'submit', ['attr' => ['class' => 'btn btn-submit btn-form']])
            ->add('Cancel', 'static', [
                'tag'     => 'a',
                'label'   => false,
                'value'   => 'Cancel',
                'attr'    => [
                    'class' => 'btn btn-cancel',
                    'href'  => route('activity.show', $activityId)
                ],
                'wrapper' => false
            ]);
    }
}
