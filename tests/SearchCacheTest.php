<?php

namespace SelrahcD\SearchCache\Tests;

use Mockery\Mock;
use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;

class SearchCacheTest extends \PHPUnit_Framework_TestCase
{

    private $searchResultStore;

    /**
     * @var SearchCache
     */
    private $searchCache;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->searchResultStore = \Mockery::mock(SearchResultsStore::class);
        $this->searchCache = new SearchCache($this->searchResultStore);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Mockery::close();
    }


    public function testStoreReturnsSameKeyIfSearchParametersAndResultsAreTheSame()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $this->searchResultStore->shouldReceive('store');

        $key1 = $this->searchCache->store($params, $results);
        $key2 = $this->searchCache->store($params, $results);

        $this->assertEquals($key1, $key2);
    }

    public function testStoreReturnsDifferentKeyIfSearchParametersArentTheSame()
    {
        $params1 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $params2 = [
            'name' => 'test2',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $this->searchResultStore->shouldReceive('store');

        $key1 = $this->searchCache->store($params1, $results);
        $key2 = $this->searchCache->store($params2, $results);

        $this->assertNotEquals($key1, $key2);
    }

    public function testStoreReturnsDifferentKeyIfSearchResultArentTheSame()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results1 = [1, 'AA', 3, "HUG76767"];
        $results2 = [1, 'AA'];

        $this->searchResultStore->shouldReceive('store');

        $key1 = $this->searchCache->store($params, $results1);
        $key2 = $this->searchCache->store($params, $results2);

        $this->assertNotEquals($key1, $key2);
    }

    public function testStoreReturnsDifferentKeyIfSearchResultsArentInTheSameOrder()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results1 = [1, 'AA', 3, "HUG76767"];
        $results2 = [1, 'AA', "HUG76767", 3];

        $this->searchResultStore->shouldReceive('store');

        $key1 = $this->searchCache->store($params, $results1);
        $key2 = $this->searchCache->store($params, $results2);


        $this->assertNotEquals($key1, $key2);
    }

    public function testStoreRecordsResults()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

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
