<?php

namespace SelrahcD\SearchCache\Tests;

use Mockery\Mock;
use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
use SelrahcD\SearchCache\KeyGenerators\KeyGenerator;
use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;

class SearchCacheTest extends \PHPUnit_Framework_TestCase
{

    private $searchResultStore;

    /**
     * @var SearchCache
     */
    private $searchCache;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->searchResultStore = \Mockery::mock(SearchResultsStore::class);
        $this->keyGenerator = \Mockery::mock(KeyGenerator::class);
        $this->searchCache = new SearchCache($this->searchResultStore, $this->keyGenerator);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testStoreResultRecordsResults()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('aKey');

        $this->searchResultStore
            ->shouldReceive('store')
            ->once()
            ->with(
                \Mockery::mustBe(new SearchResult('aKey', $result))
            );

        $this->searchCache->storeResult($result);
    }

    public function testStoreResultReturnsKey()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('aKey');

        $this->searchResultStore
            ->shouldReceive('store');

        $this->assertEquals('aKey', $this->searchCache->storeResult($result));
    }

    public function testGetResultsReturnsResultsAssociatedWithKey()
    {
        $result = [1, 2, 3];

        $this->searchResultStore
            ->shouldReceive('getResult')
            ->with('key')
            ->andReturn($result);

        $this->assertEquals($result, $this->searchCache->getResult('key'));
    }

    public function testStoreSharedResultRecordsResults()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey');

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(
                \Mockery::any(),
                \Mockery::mustBe($result)
            );

        $this->searchCache->storeSharedResult($params, $result);
    }

    public function testGetCopyOfSharedReturnsANewKeyIfAMatchingSearchResultIsStored()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andReturn(array('AA'));

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('newKey');

        $this->searchResultStore
            ->shouldReceive('store');

        $this->assertEquals('newKey', $this->searchCache->getCopyOfSharedResult($params));
    }

    public function testGetCopyOfSharedResultReturnsADifferentKeyEachTimeItsCalled()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andReturn(array('AA'));

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->once()
            ->andReturn('newKey');

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->once()
            ->andReturn('anOtherNewKey');

        $this->searchResultStore
            ->shouldReceive('store');

        $this->assertNotEquals($this->searchCache->getCopyOfSharedResult($params), $this->searchCache->getCopyOfSharedResult($params));
    }

    public function testGetCopyOfSharedResultStoresACopyOfResultWithNewKey()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result = [1, 'AA', 3, "HUG76767"];

        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andReturn($result);

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('newKey');

        $this->searchResultStore
            ->shouldReceive('store')
            ->with(
                \Mockery::mustBe(new SearchResult('newKey', $result))
            );

        $this->assertEquals('newKey', $this->searchCache->getCopyOfSharedResult($params));
    }

    /**
     * @expectedException SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException
     */
    public function testGetCopyOfSharedResultThrowsNotFoundSharedResultExceptionIfNoPreviousResultFound()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->with(\Mockery::mustBe($params))
            ->andReturn('key1');

        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andThrow(new NotFoundSharedSearchResultException);

        $this->searchCache->getCopyOfSharedResult($params);
    }

    /**
     * @expectedException SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException
     */
    public function testGetResultThrowsNotFoundSearchResultExceptionIfNoResultFound()
    {
        $this->searchResultStore
            ->shouldReceive('getResult')
            ->with('key')
            ->andThrow(new NotFoundSearchResultException);

        $this->searchCache->getResult('key');
    }

    public function testHasSharedResultReturnsTrueIfSharedResultIsStored()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->searchResultStore
            ->shouldReceive('getSharedResult');

        $this->assertTrue($this->searchCache->hasSharedResult($params));
    }

    public function testHasSharedResultReturnsFalseIfNoSharedResultIsStore()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andThrow(new NotFoundSharedSearchResultException);

        $this->assertFalse($this->searchCache->hasSharedResult($params));
    }

}
