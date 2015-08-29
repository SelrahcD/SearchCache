<?php

namespace SelrahcD\SearchCache\Tests\Stubs\SearchResultsStores;

use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;
use SelrahcD\SearchCache\SharedSearchResult;

final class InMemorySearchResultStore implements SearchResultsStore
{

    private $searchResults = [];

    private $sharedSearchResults = [];

    /**
     * Stores a search result using a key
     * @param SearchResult $searchResult
     * @return void
     */
    public function store(SearchResult $searchResult)
    {
        $this->searchResults[$searchResult->getKey()] = $searchResult;
    }

    /**
     * Stores a shared result using a key
     * @param SharedSearchResult $searchResult
     * @return void
     */
    public function storeSharedResult(SharedSearchResult $searchResult)
    {
        $this->sharedSearchResults[$searchResult->getKey()] = $searchResult;
    }

    /**
     * Retrieves a result using a key
     * @param $key
     * @return SearchResult | null
     */
    public function getResult($key)
    {
        return isset($this->searchResults[$key])? $this->searchResults[$key] : null;
    }

    /**
     * Retrieves a shared result using key
     * @param $key
     * @return SharedSearchResult | null
     */
    public function getSharedResult($key)
    {
        return isset($this->sharedSearchResults[$key])? $this->sharedSearchResults[$key] : null;
    }

    public function containsResult(array $expectedResult, \DateTimeImmutable $expirationDate)
    {
        $filteredArray = array_filter($this->searchResults, function($storedResult) use($expectedResult, $expirationDate) {
            return $storedResult->getResult() == $expectedResult
            && $storedResult->getExpirationDate() == $expirationDate;
        });

        return !empty($filteredArray);
    }

    public function containsSharedResult(array $expectedResult)
    {
        $filteredArray = array_filter($this->sharedSearchResults, function($storedResult) use($expectedResult) {
            return $storedResult->getResult() == $expectedResult;
        });

        return !empty($filteredArray);
    }
}