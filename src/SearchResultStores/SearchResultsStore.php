<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SharedSearchResult;

interface SearchResultsStore
{
    /**
     * Stores a search result using a key
     * @param SearchResult $searchResult
     * @return void
     */
    public function store(SearchResult $searchResult);

    /**
     * Stores a shared result using a key
     * @param SharedSearchResult $searchResult
     * @return void
     */
    public function storeSharedResult(SharedSearchResult $searchResult);

    /**
     * Retrieves a result using a key
     * @param $key
     * @return SearchResult | null
     */
    public function getResult($key);

    /**
     * Retrieves a shared result using key
     * @param $key
     * @return SharedSearchResult | null
     */
    public function getSharedResult($key);
}