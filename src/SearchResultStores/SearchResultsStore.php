<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\SearchResult;

interface SearchResultsStore
{
    /**
     * Stores a search result using a key
     * @param SearchResult $searchResult
     * @return string
     */
    public function store(SearchResult $searchResult);

    /**
     * Stores a shared result using a key
     * @param $key
     * @param array $results
     * @return mixed
     */
    public function storeSharedResult($key, array $results);

    /**
     * Retrieves a result using a key
     * @param $key
     * @return mixed
     * @throws NotFoundSearchResultException if not matching result found
     */
    public function getResult($key);

    /**
     * Retrieves a shared result using key
     * @param $key
     * @return mixed
     * @throws NotFoundSearchResultException if not matching result found
     */
    public function getSharedResult($key);
}