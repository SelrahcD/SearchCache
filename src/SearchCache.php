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
    public function storeSharedResult($params, $results)
    {
        $key = $this->keyGenerator->generateSharedKey($params);

        $this->searchResultsStore->store($key, $results);

        return $key;
    }
}