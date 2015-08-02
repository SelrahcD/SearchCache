<?php

namespace SelrahcD\SearchCache\Tests\SearchResultStores;


use Mockery\Mock;
use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SearchResultStores\PredisSearchResultStore;
use SelrahcD\SearchCache\SharedSearchResult;

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
        $this->redisClient = \Mockery::mock('Predis\Client');
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

        $this->resultStore->store(new SearchResult('key', [1,2,3]));
    }

    public function testReturnsResultMatchingKey()
    {
        $this->redisClient
            ->shouldReceive('smembers')
            ->with(\Mockery::mustBe('key'))
            ->andReturn([1,2,3]);

        $this->assertInstanceOf('SelrahcD\SearchCache\SearchResult', $this->resultStore->getResult('key'));
        $this->assertEquals([1,2,3], $this->resultStore->getResult('key')->getResult());
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

        $this->resultStore->storeSharedResult(new SharedSearchResult('sharedKey', [1,2,3]));
    }

    public function testGetSharedResultStoresReturnsResultMatchingKey()
    {
        $this->redisClient
            ->shouldReceive('smembers')
            ->with(\Mockery::mustBe('sharedKey'))
            ->andReturn([1,2,3]);

        $this->assertInstanceOf('SelrahcD\SearchCache\SharedSearchResult',  $this->resultStore->getSharedResult('sharedKey'));
        $this->assertEquals([1,2,3], $this->resultStore->getSharedResult('sharedKey')->getResult());
    }

    /**
     * @expectedException SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException
     */
    public function testGetResultThrowsNotFoundSearchResultExceptionIfCouldNotRetrieveSearchresultFromRedis()
    {
        $this->redisClient
            ->shouldReceive('smembers')
            ->with(\Mockery::mustBe('key'))
            ->andReturn(array());

        $this->assertEquals($this->resultStore->getResult('key'));
    }

    /**
     * @expectedException SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException
     */
    public function testGetSharedResultThrowsNotFoundSharedSearchResultExceptionIfCouldNotRetrieveSearchresultFromRedis()
    {
        $this->redisClient
            ->shouldReceive('smembers')
            ->with(\Mockery::mustBe('sharedKey'))
            ->andReturn(array());

        $this->assertEquals($this->resultStore->getSharedResult('sharedKey'));
    }

}
