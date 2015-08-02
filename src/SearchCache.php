<?php
namespace SelrahcD\SearchCache;

use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
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

        $this->searchResultsStore->store(new SearchResult($key, $results));

        return $key;
    }

    /**
     * Returns search results for a given key
     * @param $key
     * @return array
     * @throws NotFoundSearchResultException
     */
    public function getResult($key)
    {
        return $this->searchResultsStore->getResult($key)->getResult();
    }

    /**
     * Stores shared results and returns a key
     * @param $params
     * @param $result
     * @return string
     */
    public function storeSharedResult(array $params, array $result)
    {
        $sharedKey = $this->generateSharedKey($params);

        $this->searchResultsStore->storeSharedResult(new SharedSearchResult($sharedKey, $result));
    }

    /**
     * Finds a key for shared result
     * @param array $params
     * @return mixed
     */
    public function getCopyOfSharedResult(array $params)
    {
        $sharedKey = $this->generateSharedKey($params);

        $sharedResult = $this->searchResultsStore->getSharedResult($sharedKey);

        $newKey = $this->keyGenerator->generateKey();

        $this->searchResultsStore->store($sharedResult->createSearchResult($newKey));

        return $newKey;
    }

    /**
     * Test if a shared result is already stored for this set of params
     * @param array $params
     * @return bool
     */
    public function hasSharedResult(array $params)
    {
        $sharedKey = $this->generateSharedKey($params);

        try {
            $this->searchResultsStore->getSharedResult($sharedKey);
        }
        catch(NotFoundSharedSearchResultException $exception) {
            return false;
        }

        return true;
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