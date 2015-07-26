<?php
namespace  SelrahcD\SearchCache;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;

final class SearchCache
{
    /**
     * @var SearchResultsStore
     */
    private $searchResultsStore;

    /**
     * SearchCache constructor.
     * @param SearchResultsStore $searchResultsStore
     */
    public function __construct(SearchResultsStore $searchResultsStore)
    {
        $this->searchResultsStore = $searchResultsStore;
    }

    /**
     * Stores results and returns associated key
     * @param array $params
     * @param array $results
     * @return string
     */
    public function store(array $params, array $results)
    {
        $key = $this->generateKey($params, $results);

        $this->searchResultsStore->store($key, $results);

        return $key;
    }

    /**
     * Generates the key based on search params and results
     * @param array $params
     * @param array $results
     * @return string
     */
    private function generateKey(array $params, array $results)
    {
        return md5(serialize($params) . serialize($results));
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