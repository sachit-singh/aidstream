<?php namespace App\Migration\Migrator\Data;

use App\Models\Organization\Organization;

/**
 * Class OrganizationQuery
 * @package App\Migration\Migrator\Data
 */
class OrganizationQuery extends Query
{
    /**
     * @param array $accountIds
     * @return array
     */
    public function executeFor(array $accountIds)
    {
        $this->initDBConnection();

        $organizations      = [];
        $unmigratedAccounts = $this->check($accountIds);

        foreach ($accountIds as $accountId) {
            $organizationData = $this->simpleValues($accountId);

            if (in_array($accountId, $unmigratedAccounts)) {
                if ($organization = getOrganizationFor($accountId)) {
                    $organizationData['reporting_org'] = $this->reportingOrgValues($organization->id);
                }
            }

            $organizations[] = $organizationData;
        }

        return $organizations;
    }

    /**
     * Get Simple values.
     * @param $accountId
     * @return array
     */
    protected function simpleValues($accountId)
    {
        $account = $this->connection->table('account')
                                    ->select('*')
                                    ->where('id', '=', $accountId)
                                    ->first();

        $publishedToRegistry = $this->connection->table('organisation_published')
                                                ->select('pushed_to_registry')
                                                ->where('publishing_org_id', '=', $accountId)
                                                ->first();

        $timestamp = ($org = $this->connection->table('iati_organisation')
                                              ->select('@last_updated_datetime as time')
                                              ->where('account_id', '=', $accountId)
                                              ->first()) ? $org->time : '';

        $organization = [
            'id'                    => $accountId,
            'user_identifier'       => $account->username,
            'name'                  => $account->name,
            'address'               => $account->address,
            'telephone'             => $account->telephone,
            'status'                => $account->status,
            'organization_url'      => $account->url,
            'disqus_comments'       => ($comment = $account->disqus_comments) ? $comment : 0,
            'twitter'               => $account->twitter,
            'published_to_registry' => $publishedToRegistry ? $publishedToRegistry->pushed_to_registry : 0,
            'logo'                  => $account->file_name ? $account->file_name : null,
            'logo_url'              => $account->file_name ? '/files/logos/' . $account->file_name : null,
            'created_at'            => $timestamp,
            'updated_at'            => $timestamp
        ];

        return $organization;
    }

    /**
     * Get Reporting Organization values.
     * @param $organizationId
     * @return array
     */
    protected function reportingOrgValues($organizationId)
    {
        $reportingOrgReferenceType = $this->connection->table('iati_organisation/reporting_org')
                                                      ->select('@ref as reporting_organization_identifier', '@type as reporting_organization_type')
                                                      ->where('organisation_id', '=', $organizationId)
                                                      ->first();

        $referenceTypeCode = fetchCode($reportingOrgReferenceType->reporting_organization_type, 'OrganisationType');

        $reportingOrgNarratives = $this->connection->table('iati_organisation/reporting_org/narrative')
                                                   ->select('text', '@xml_lang as xml_lang')
                                                   ->where('reporting_org_id', '=', $organizationId)
                                                   ->get();

        $reportingOrgNarrative = [];

        foreach ($reportingOrgNarratives as $narrative) {
            $languageCode            = getLanguageCodeFor($narrative->xml_lang);
            $reportingOrgNarrative[] = ['narrative' => $narrative->text, 'language' => $languageCode];
        }

        return [
            [
                'reporting_organization_identifier' => $reportingOrgReferenceType->reporting_organization_identifier,
                'reporting_organization_type'       => $referenceTypeCode,
                'narrative'                         => $reportingOrgNarrative
            ]
        ];
    }

    protected function check($accountIds)
    {
        $unmigratedAccounts = [];

        foreach ($accountIds as $accountId) {
            $organization = null;
            $organization = app()->make(Organization::class)->query()->select('*')->where('id', '=', $accountId)->first();

            if ($organization === null) {
                $unmigratedAccounts[] = $accountId;
            }
        }

        return $unmigratedAccounts;
    }
}
