<?php

namespace SelrahcD\SearchCache\Tests;

use Mockery\Mock;
use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;
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

    const NOW = '1989-01-13 14:50:17';

    const VALID_EXPIRATION_DATE = '1989-01-13 14:50:20';

    const EXPIRED_EXPIRATION_DATE = '1989-01-13 12:00:00';

    /**
     * @var SearchResultsStore
     */
    private $searchResultStore;

    /**
     * @var SearchCache
     */
    private $searchCache;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    protected function setUp()
    {
        $this->searchResultStore = \Mockery::mock('SelrahcD\SearchCache\SearchResultStores\SearchResultsStore', function($mock) {
            $mock->shouldReceive('store')->byDefault();
        });

        $this->keyGenerator = \Mockery::mock('SelrahcD\SearchCache\KeyGenerators\KeyGenerator');
        $this->searchCache = new TestableSearchCache($this->searchResultStore, $this->keyGenerator);

        $this->setNow(new \DateTimeImmutable(self::NOW));

        $this->keyGeneratorWillGenerateThisKeys(['aKey', 'someOtherKey']);
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testStoreResultRecordsResults()
    {
        $result = [1, 'AA', 3, "HUG76767"];

        $this->storeShouldStore('aKey', $result);

        $this->searchCache->storeResult($result);
    }

    public function testStoreResultReturnsKey()
    {
        $this->assertTrue(is_string($this->searchCache->storeResult([])));
    }

    public function testGetResultsReturnsResultsAssociatedWithKey()
    {
        $result = [1, 2, 3];

        $this->storeContainsValidResult('key', $result);

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

        $this->shouldStoreSharedResult($result);

        $this->searchCache->storeSharedResult($params, $result);
    }

    public function testGetCopyOfSharedReturnsAKeyIfAMatchingSearchResultIsStored()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->storeContainsSharedResult($params, ['AA']);

        $this->assertTrue(is_string($this->searchCache->getCopyOfSharedResult($params)));
    }

    public function testGetCopyOfSharedResultReturnsADifferentKeyEachTimeItsCalled()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $this->storeContainsSharedResult($params, ['AA']);

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
            ->andReturn(new SharedSearchResult('aSharedKey', $result));

        $this->storeShouldStore('aKey', $result);

        $this->searchCache->getCopyOfSharedResult($params);
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
        $this->storeDoesntContainSearchResultWithKey('key');

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
        $result = [1, 'AA', 3, "HUG76767"];

        $this->storeShouldStore('aKey', $result);

        $this->searchCache->storeResult($result);
    }

    public function testStoreResultStoresResultWithExpirationDateSetWithProvidedTTL()
    {
        $overwriteTTL = 3;

        $result = [1, 'AA', 3, "HUG76767"];

        $this->searchCache->setSearchResultTTL($overwriteTTL);

        $this->storeShouldStore('aKey', $result, $overwriteTTL);

        $this->searchCache->storeResult($result);
    }

    /**
     * @expectedException SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException
     */
    public function testGetResultThrowNotFoundSearchResultExceptionIfSearchResultHasExpired()
    {
        $this->storeContainsExpiredResult('key');

        $this->searchCache->getResult('key');
    }

    /*************************************
     * Helpers
     *************************************/

    private function setNow(\DateTimeImmutable $now)
    {
        $this->searchCache->setNow($now);
    }

    private function keyGeneratorWillGenerateThisKeys(array $keys)
    {
        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->andReturnValues($keys);
    }

    /**
     * @param $key
     * @param $result
     */
    private function storeContainsValidResult($key, $result)
    {
        $this->searchResultStore
            ->shouldReceive('getResult')
            ->with($key)
            ->andReturn(new SearchResult($key, $result, new \DateTimeImmutable(self::VALID_EXPIRATION_DATE)));
    }

    /**
     * @param $key
     */
    private function storeContainsExpiredResult($key)
    {
        $this->searchResultStore
            ->shouldReceive('getResult')
            ->with($key)
            ->andReturn(new SearchResult($key, [], new \DateTimeImmutable(self::EXPIRED_EXPIRATION_DATE)));
    }

    /**
     * @param $result
     */
    private function storeShouldStore($key, $result, $ttl = SearchCache::DEFAULT_SEARCH_RESULT_TTL)
    {
        $expirationDate = new \DateTimeImmutable(self::NOW);
        $expirationDate = $expirationDate->modify('+' . $ttl . 'second');

        $this->searchResultStore
            ->shouldReceive('store')
            ->once()
            ->with(
                \Mockery::mustBe(new SearchResult($key, $result, $expirationDate))
            );
    }

    /**
     * @param $result
     */
    private function shouldStoreSharedResult($result)
    {
        $this->searchResultStore
            ->shouldReceive('storeSharedResult')
            ->once()
            ->with(\Mockery::on(function ($searchResult) use ($result) {
                return $searchResult->getResult() === $result;
            }));
    }

    private function storeContainsSharedResult($params, $result)
    {
        $this->searchResultStore
            ->shouldReceive('getSharedResult')
            ->andReturn(new SharedSearchResult('aSharedKey', $result));
    }

    private function storeDoesntContainSearchResultWithKey($key)
    {
        $this->searchResultStore
            ->shouldReceive('getResult')
            ->with($key)
            ->andReturn(null);
    }
}
