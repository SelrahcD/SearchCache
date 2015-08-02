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
     * @param array $results
     * @return string
     */
    public function storeResult(array $results)
    {
        $key = $this->keyGenerator->generateKey();

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
     * Stores shared results and returns a key
     * @param $params
     * @param $results
     * @return string
     */
    public function storeSharedResult(array $params, array $results)
    {
        $sharedKey = $this->generateSharedKey($params);

        $this->searchResultsStore->storeSharedResult($sharedKey, $results);
    }

    /**
     * Finds a key for shared result
     * @param array $params
     * @return mixed
     */
    public function getCopyOfSharedResult(array $params)
    {
        $sharedKey = $this->generateSharedKey($params);

        if($result = $this->searchResultsStore->getSharedResult($sharedKey)) {

            $newKey = $this->keyGenerator->generateKey();

            $this->searchResultsStore->store($newKey, $result);

            return $newKey;
        }

        return null;
    }

    /**
     * Generates a shared key based on search parameters
     * @param array $params
     * @return string
     */
    private function generateSharedKey(array $params)
    {
        $this->orderParameters($params);

        return md5(serialize($params));
    }

    /**
     * Reorders search parameters
     * @param array $params
     */
    private function orderParameters(array &$params)
    {
        ksort($params);
    }
}