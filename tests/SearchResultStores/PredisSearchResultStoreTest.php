<?php

namespace SelrahcD\SearchCache\Tests\SearchResultStores;


use Mockery\Mock;
use Predis\Client;
use SelrahcD\SearchCache\SearchResultStores\PredisSearchResultStore;

class PredisSearchResultStoreTest extends \PHPUnit_Framework_TestCase
{
    private $resultStore;

    private $redisClient;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->redisClient = \Mockery::mock(Client::class);
        $this->resultStore = new PredisSearchResultStore($this->redisClient);
    }


    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testStoresResultUsingKey()
    {
        $this->redisClient
            ->shouldReceive('sadd')
            ->once()
            ->with(
                \Mockery::mustBe('key'),
                \Mockery::mustBe([1,2,3])
            );

        $this->resultStore->store('key', [1,2,3]);
    }

    public function testReturnsResultMatchingKey()
    {
        $this->redisClient
            ->shouldReceive('smembers')
            ->with(\Mockery::mustBe('key'))
            ->andReturn([1,2,3]);

        $this->assertEquals([1,2,3], $this->resultStore->getResult('key'));
    }

    public function testStoreSharedResultStoresResultUsingSharedKey()
    {
        $this->redisClient
            ->shouldReceive('sadd')
            ->once()
            ->with(
                \Mockery::mustBe('sharedKey'),
                \Mockery::mustBe([1,2,3])
            );

        $this->resultStore->store('sharedKey', [1,2,3]);
    }

    public function testGetSharedResultStoresReturnsResultMatchingKey()
    {
        $this->redisClient
            ->shouldReceive('smembers')
            ->with(\Mockery::mustBe('sharedKey'))
            ->andReturn([1,2,3]);

        $this->assertEquals([1,2,3], $this->resultStore->getSharedResult('sharedKey'));
    }

}
