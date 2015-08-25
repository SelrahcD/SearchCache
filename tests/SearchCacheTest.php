<?php

namespace SelrahcD\SearchCache\Tests;

use Mockery\Mock;
use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SharedSearchResult;

class TestableSearchCache extends SearchCache
{
    private $now = null;

    protected function now()
    {
        return $this->now ? : new \DateTimeImmutable();
    }

    public function setNow(\DateTimeImmutable $now)
    {
        $this->now = $now;
    }
}

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
        $this->searchResultStore = \Mockery::mock('SelrahcD\SearchCache\SearchResultStores\SearchResultsStore');
        $this->keyGenerator = \Mockery::mock('SelrahcD\SearchCache\KeyGenerators\KeyGenerator');
        $this->searchCache = new TestableSearchCache($this->searchResultStore, $this->keyGenerator);
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
        $this->setNow(new \DateTimeImmutable('1989-01-13 14:50:17'));
        $result = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('aKey');

        $this->searchResultStore
            ->shouldReceive('store')
            ->once()
            ->with(
                \Mockery::mustBe(new SearchResult('aKey', $result, new \DateTimeImmutable('1989-01-13 15:00:17')))
            );

        $this->searchCache->storeResult($result);
    }

    public function testStoreResultReturnsKey()
    {
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
            ->andReturn(new SearchResult('key', $result, new \DateTimeImmutable()));

        $this->assertTrue(is_array($this->searchCache->getResult('key')));
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
            ->with(\Mockery::on(function($searchResult) use ($result) {
              return $searchResult->getResult() === $result;
            }));

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
            ->andReturn(new SharedSearchResult('aSharedKey', array('AA')));

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
            ->andReturn(new SharedSearchResult('aSharedKey', array('AA')));

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
        $this->setNow(new \DateTimeImmutable('1989-01-13 14:50:17'));

        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result = [1, 'AA', 3, "HUG76767"];

        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andReturn(new SharedSearchResult('aSharedKey', $result));


        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('newKey');

        $this->searchResultStore
            ->shouldReceive('store')
            ->with(
                \Mockery::mustBe(new SearchResult('newKey', $result, new \DateTimeImmutable('1989-01-13 15:00:17')))
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
            ->andReturn(null);
        
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

    public function testStoringSharedResultSeveralTimesWithSameParametersUseTheSameKey()
    {

        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result1 = [1, 'AA', 3, "HUG76767"];
        $result2 = [1, 'AA', 3, "Oho"];

        $this->keyGenerator
            ->shouldReceive('generateKey');

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function($searchResult) use (&$key1, $result1) {
                if($searchResult->getResult() == $result1) {
                    $key1 = $searchResult->getKey();
                    return true;
                }
                return false;
            }));

        $this->searchCache->storeSharedResult($params, $result1);

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function($searchResult) use (&$key2, $result2) {
                if($searchResult->getResult() == $result2) {
                    $key2 = $searchResult->getKey();
                    return true;
                }
                return false;
            }));

        $this->searchCache->storeSharedResult($params, $result2);

        $this->assertEquals($key1, $key2);
    }

    public function testStoringSharedResultWithSameParametersInDifferentSearchSpaceDoesntUseSameSharedKey()
    {
        $searchCache1 = new SearchCache($this->searchResultStore, $this->keyGenerator, 'poney');
        $searchCache2 = new SearchCache($this->searchResultStore, $this->keyGenerator, 'chat');

        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result1 = [1, 'AA', 3, "HUG76767"];
        $result2 = [1, 'AA', 3, "AAA"];

        $key1 = "AA";
        $key2 = "BB";

        $this->keyGenerator
            ->shouldReceive('generateKey');

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function($searchResult) use (&$key1, $result1) {
                if($searchResult->getResult() == $result1) {
                    $key1 = $searchResult->getKey();
                    return true;
                }
                return false;
            }));

        $searchCache1->storeSharedResult($params, $result1);

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function($searchResult) use (&$key2, $result2) {
                if($searchResult->getResult() == $result2) {
                    $key2 = $searchResult->getKey();
                    return true;
                }
                return false;
            }));

        $searchCache2->storeSharedResult($params, $result2);

        $this->assertNotEquals($key1, $key2);
    }

    public function testStoringSharedResultWithSameParametersInTheSameSearchSpaceUsesTheSameKey()
    {
        $searchCache1 = new SearchCache($this->searchResultStore, $this->keyGenerator, 'poney');
        $searchCache2 = new SearchCache($this->searchResultStore, $this->keyGenerator, 'poney');

        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $result1 = [1, 'AA', 3, "HUG76767"];
        $result2 = [1, 'AA', 3, "AAA"];

        $key1 = "AA";
        $key2 = "BB";

        $this->keyGenerator
            ->shouldReceive('generateKey');

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function($searchResult) use (&$key1, $result1) {
                if($searchResult->getResult() == $result1) {
                    $key1 = $searchResult->getKey();
                    return true;
                }
                return false;
            }));

        $searchCache1->storeSharedResult($params, $result1);

        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function($searchResult) use (&$key2, $result2) {
                if($searchResult->getResult() == $result2) {
                    $key2 = $searchResult->getKey();
                    return true;
                }
                return false;
            }));

        $searchCache2->storeSharedResult($params, $result2);

        $this->assertEquals($key1, $key2);
    }

    public function testStoreResultStoresResultWithExpirationDateSetWithDefaultTTLIfNoneProvided()
    {
        $this->setNow(new \DateTimeImmutable('1989-01-13 14:50:17'));

        $result = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('aKey');

        $this->searchResultStore
            ->shouldReceive('store')
            ->once()
            ->with(
                \Mockery::mustBe(new SearchResult('aKey', $result, new \DateTimeImmutable('1989-01-13 15:00:17')))
            );

        $this->searchCache->storeResult($result);
    }

    public function testStoreResultStoresResultWithExpirationDateSetWithProvidedTTL()
    {
        $this->setNow(new \DateTimeImmutable('1989-01-13 14:50:17'));

        $this->searchCache->setSearchResultTTL(3);

        $result = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturn('aKey');

        $this->searchResultStore
            ->shouldReceive('store')
            ->once()
            ->with(
                \Mockery::mustBe(new SearchResult('aKey', $result, new \DateTimeImmutable('1989-01-13 14:50:20')))
            );

        $this->searchCache->storeResult($result);
    }

    private function setNow(\DateTimeImmutable $now)
    {
        $this->searchCache->setNow($now);
    }
}
