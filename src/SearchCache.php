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
     * @param KeyGenerator $keyGenerator
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
    public function storeResult(array $params, array $results)
    {
        $key = $this->keyGenerator->generatePrivateKey($params, $results);

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

    /**
     * Stores shared results and returns associated key
     * @param $params
     * @param $results
     * @return string
     */
    public function storeSharedResult(array $params, array $results)
    {
        $key = $this->keyGenerator->generateSharedKey($params);

        $this->searchResultsStore->store($key, $results);

        return $key;
    }

    /**
     * Finds a key for shared result
     * @param array $params
     * @return mixed
     */
    public function findSharedResult(array $params)
    {
        $key = $this->keyGenerator->generateSharedKey($params);

        $result = $this->searchResultsStore->getResult($key);;

        $newKey = $this->keyGenerator->createCopyOfKey($key);

        $this->searchResultsStore->store($newKey, $result);

        return $newKey;
    }
}