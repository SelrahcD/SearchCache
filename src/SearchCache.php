<?php
namespace SelrahcD\SearchCache;

use SelrahcD\SearchCache\KeyGenerators\KeyGenerator;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;

final class SearchCache
{
    /**
     * @var SearchResultsStore
     */
    private $searchResultsStore;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    /**
     * SearchCache constructor.
     * @param SearchResultsStore $searchResultsStore
     */
    public function __construct(SearchResultsStore $searchResultsStore, KeyGenerator $keyGenerator)
    {
        $this->searchResultsStore = $searchResultsStore;
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * Stores results and returns associated key
     * @param array $params
     * @param array $results
     * @return string
     */
    public function store(array $params, array $results)
    {
        $key = $this->keyGenerator->generateKey($params, $results);

        $this->searchResultsStore->store($key, $results);

        return $key;
    }

    /**
     * Returns search results for a given key
     * @param $key
     * @return array
     */
    public function getResult($key)
    {
        return $this->searchResultsStore->getResult($key);
    }
}