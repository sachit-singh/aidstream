<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;

/**
 * Get Organization for an Account.
 * @param $accountId
 * @return mixed
 */
function getOrganizationFor($accountId)
{
    return app()->make(DatabaseManager::class)
                ->connection('mysql')
                ->table('iati_organisation')
                ->select('id')
                ->where('account_id', '=', $accountId)
                ->first();
}

/**
 * Get the Language code for a Language with the given id.
 * @param $languageId
 * @return string
 */
function getLanguageCodeFor($languageId)
{
    return ($language = app()->make(DatabaseManager::class)
                             ->connection('mysql')
                             ->table('Language')
                             ->select('Code')
                             ->where('id', '=', $languageId)
                             ->first()) ? $language->Code : '';
}

/**
 * Get an Activity Identifier object.
 * @param $activityId
 * @return mixed
 */
function getActivityIdentifier($activityId)
{
    return app()->make(DatabaseManager::class)
                ->connection('mysql')
                ->table('iati_identifier')
                ->select('activity_identifier', 'text')
                ->where('activity_id', '=', $activityId)
                ->first();
}

/**
 * Fetch Narratives from a given table.
 * @param $value
 * @param $table
 * @param $column
 * @return mixed
 */
function fetchNarratives($value, $table, $column)
{
    return app()->make(DatabaseManager::class)
                ->connection('mysql')
                ->table($table)
                ->select('*', '@xml_lang as xml_lang')
                ->where($column, '=', $value)
                ->get();
}

/**
 * Fetch any given field from any given table on the conditions specified.
 * @param $field
 * @param $table
 * @param $column
 * @param $value
 * @return Builder
 */
function getBuilderFor($field, $table, $column, $value)
{
    return app()->make(DatabaseManager::class)
                ->connection('mysql')
                ->table($table)
                ->select($field)
                ->where($column, '=', $value);
}

/**
 * Fetch code from a given table.
 * @param $id
 * @param $table
 * @param $act
 * @return string
 */
function fetchCode($id, $table, $act)
{
    return ($code = app()->make(DatabaseManager::class)
                         ->connection('mysql')
                         ->table($table)
                         ->select('Code')
                         ->where('id', '=', $id)
                         ->first()) ? $code->Code : '';
}