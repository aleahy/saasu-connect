<?php


namespace Aleahy\SaasuConnect\Tests;


use Aleahy\SaasuConnect\Entities\BaseEntity;

class SaasuAPITest extends TestCase
{
    /**
     * @test
     */
    public function test_it_can_get_the_correct_uri_for_post_request()
    {
        $expected = 'Base?FileId=' . $this->fileId;
        $uri = $this->api->getUriForPost(BaseEntity::class);
        $this->assertEquals($expected, $uri);
    }

    /**
     * @test
     */
    public function test_it_can_get_the_correct_uri_for_get_by_id_request()
    {
        $expected = 'Base/' . '1234' . '?FileId=' . $this->fileId;
        $uri = $this->api->getUriForGetById(BaseEntity::class, 1234);
        $this->assertEquals($expected, $uri);
    }

    /**
     * @test
     */
    public function test_it_can_get_the_correct_uri_for_search()
    {
        $expected = 'Bases?FileId=' . $this->fileId . '&GivenName=Alex&Email=alex%40example.com';
        $uri = $this->api->getUriForSearch(BaseEntity::class, [
            'GivenName' => 'Alex',
            'Email' => 'alex@example.com'
        ]);
        $this->assertEquals($expected, $uri);
    }
}