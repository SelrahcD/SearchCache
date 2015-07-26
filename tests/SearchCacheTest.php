<?php

namespace SelrahcD\SearchCache\Tests;

use Mockery\Mock;
use SelrahcD\SearchCache\KeyGenerators\KeyGenerator;
use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;

class SearchCacheTest extends \PHPUnit_Framework_TestCase
{

    private $searchResultStore;

    /**
     * @var SearchCache
     */
    private $searchCache;

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

    public function testStoreRecordsResults()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey');

        $this->searchResultStore->shouldReceive('store')->once();
        $key = $this->searchCache->store($params, $results);

        $this->searchResultStore
            ->shouldReceive('store')
            ->once()
            ->with(
                \Mockery::mustBe($key),
                \Mockery::mustBe($results)
            );

        $this->searchCache->store($params, $results);
    }

    public function testStoreReturnsKey()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $this->keyGenerator
            ->shouldReceive('generateKey')
            ->with(\Mockery::mustBe($params),
                \Mockery::mustBe($results))
            ->andReturn('aKey');

        $this->searchResultStore->shouldReceive('store')->once();
        $key = $this->searchCache->store($params, $results);

        $this->searchResultStore
            ->shouldReceive('store');

        $this->assertEquals('aKey', $this->searchCache->store($params, $results));
    }

    public function testGetResultsReturnsResultsAssociatedWithKey()
    {
        $results = [1, 2, 3];

        $this->searchResultStore
            ->shouldReceive('getResult')
            ->with('key')
            ->andReturn($results);

        $this->assertEquals($results, $this->searchCache->getResult('key'));
    }
}
