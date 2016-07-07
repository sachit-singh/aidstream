<?php namespace App\Core\V201\Forms\Settings;


use App\Core\Form\BaseForm;
use App\Models\Settings;

class DefaultValues extends BaseForm
{

    protected $settings;
    protected $defaultFieldGroups;

    public function __construct(Settings $settings)
    {
        $this->settings           = $settings->where('organization_id', session('org_id'))->first();
        $this->defaultFieldGroups = (array) (($this->settings) ? $this->settings->default_field_groups : []);
    }

    public function buildForm()
    {
        $this->addSelect('default_currency', $this->getCodeList('Currency', 'Organization'), 'Default Currency', $this->addHelpText('activity_defaults-default_currency', false), null, true)
             ->addSelect(
                 'default_language',
                 $this->getCodeList('Language', 'Organization'),
                 'Default Language',
                 $this->addHelpText('activity_defaults-default_language', false),
                 config('app.default_language'),
                 true
             )
             ->add('default_hierarchy', 'text', ['help_block' => $this->addHelpText('activity_defaults-hierarchy', false)])
             ->add('linked_data_uri', 'text', ['label' => 'Linked Data Default']);
        $this->addSelect(
            'default_collaboration_type',
            $this->getCodeList('CollaborationType', 'Organization'),
            'Default Collaboration Type',
            $this->addHelpText('activity_defaults-default_collaboration_type', false),
            null,
            false,
            getVal($this->defaultFieldGroups, [0, 'Classifications', 'collaboration_type']) == "" ? ['wrapper' => ['class' => 'form-group hidden']] : []
        );
        $this->addSelect(
            'default_flow_type',
            $this->getCodeList('FlowType', 'Organization'),
            'Default Flow Type',
            $this->addHelpText('activity_defaults-default_flow_type', false),
            null,
            false,
            (getVal($this->defaultFieldGroups, [0, 'Classifications', 'default_flow_type']) == "") ? ['wrapper' => ['class' => 'form-group hidden']] : []
        );
        $this->addSelect(
            'default_finance_type',
            $this->getCodeList('FinanceType', 'Organization'),
            'Default Finance Type',
            $this->addHelpText('activity_defaults-default_finance_type', false),
            null,
            false,
            (getVal($this->defaultFieldGroups, [0, 'Classifications', 'default_finance_type']) == "") ? ['wrapper' => ['class' => 'form-group hidden']] : []
        );
        $this->addSelect(
            'default_aid_type',
            $this->getCodeList('AidType', 'Organization'),
            'Default Aid Type',
            $this->addHelpText('activity_defaults-default_aid_type', false),
            null,
            false,
            (getVal($this->defaultFieldGroups, [0, 'Classifications', 'default_aid_type']) == "") ? ['wrapper' => ['class' => 'form-group hidden']] : []
        );
        $this->addSelect(
            'default_tied_status',
            $this->getCodeList('TiedStatus', 'Organization'),
            'Default Tied Status',
            $this->addHelpText('activity_defaults-default_tied_status', false),
            null,
            false,
            (getVal($this->defaultFieldGroups, [0, 'Classifications', 'default_tied_status']) == "") ? ['wrapper' => ['class' => 'form-group hidden']] : []
        );
        $this->add(
            'save',
            'submit',
            [
                'label'   => 'Save',
                'attr'    => ['class' => 'btn btn-primary'],
                'wrapper' => ['class' => 'form-group']

            ]
        );

    }
}