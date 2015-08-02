<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;

interface SearchResultsStore
{
    /**
     * Stores a search result using a key
     * @param $key
     * @param array $results
     * @return mixed
     */
    public function store($key, array $results);

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