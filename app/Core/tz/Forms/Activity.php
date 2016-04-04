<?php namespace App\Core\tz\Forms;

use App\Core\tz\BaseForm;

class Activity extends BaseForm
{
    /**
     * builds activity form
     */
    public function buildForm()
    {

        $this
            ->addCollection('iati_identifiers', 'Activity\Identifier', 'identifier-form', [], false)
            ->add('title', 'textarea', ['required' => true])
            ->addCollection('description', 'Description', '', [], false, 'tz')
            ->addCollection('participating_organization', 'ParticipatingOrganization', 'participation-org-form', [], false, 'tz')
            ->addSelect('activity_status', $this->getCodeList('ActivityStatus', 'Activity'), 'Activity status', null, null, true)
            ->addMultipleSelect('sector_category_code', $this->getCodeList('SectorCategory', 'Activity'), 'Sector', null, null, true)
            ->add('start_date', 'date', ['required' => true, 'attr' => ['placeholder' => 'YYYY-MM-DD']])
            ->add('end_date', 'date', ['attr' => ['placeholder' => 'YYYY-MM-DD']])
            ->addMultipleSelect('recipient_country', $this->getCodeList('Country', 'Organization'), null, null, null, true);
    }
}
