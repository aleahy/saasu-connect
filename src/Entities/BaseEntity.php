<?php


namespace Aleahy\SaasuConnect\Entities;


use GuzzleHttp\Psr7\Request;

class BaseEntity
{
    const SINGLE_ENDPOINT = null;
    const SEARCH_ENDPOINT = null;

    public static function getSearchRequest(array $attributes, $fileId) {
        if (!STATIC::SEARCH_ENDPOINT)
            throw new \Exception('Entity does not have an endpoint for search');
        $query = array_merge(['FileId' => $fileId], $attributes);
        $options = [
            'query' => $query
        ];

        return new Request('GET', STATIC::SEARCH_ENDPOINT . '?' . http_build_query($query));
    }

    public static function getGetItemByIdRequest($id, $fileId)
    {
        if (!STATIC::SEARCH_ENDPOINT)
            throw new \Exception('Entity does not have an endpoint for search');

        return new Request('GET', STATIC::SINGLE_ENDPOINT . '/' . $id . '?fileid=' . $fileId);
    }

}