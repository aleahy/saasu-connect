<?php


namespace Aleahy\SaasuConnect\Entities;

/*
 * When updating the company, the Name and LastUpdatedId
 * are required fields
 */

class Company extends BaseEntity
{
    const SINGLE_ENDPOINT = '/Company';
    const SEARCH_ENDPOINT = 'Companies';
}