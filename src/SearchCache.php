<?php
namespace SelrahcD\SearchCache;

use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
use SelrahcD\SearchCache\KeyGenerators\KeyGenerator;
use SelrahcD\SearchCache\SearchResultStores\SearchResultsStore;

class SearchCache
{
    const DEFAULT_SEARCH_RESULT_TTL = 600;

    /**
     * @var SearchResultsStore
     */
    private $searchResultsStore;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    /**
     * @var string
     */
    private $searchSpace;

    /**
     * @var int
     */
    private $searchResultTTL = self::DEFAULT_SEARCH_RESULT_TTL;

    /**
     * SearchCache constructor.
     * @param SearchResultsStore $searchResultsStore
     * @param KeyGenerator $keyGenerator
     * @param string $searchSpace
     */
    public function __construct(SearchResultsStore $searchResultsStore, KeyGenerator $keyGenerator, $searchSpace = '')
    {
        $this->searchResultsStore = $searchResultsStore;
        $this->keyGenerator = $keyGenerator;
        $this->searchSpace = $searchSpace;
    }

    /**
     * Stores results and returns associated key
     * @param array $results
     * @return string
     */
    public function storeResult(array $results)
    {
        $key = $this->keyGenerator->generateKey();

        $expirationDate = $this->getSearchResultExpirationDate();

        $this->searchResultsStore->store(new SearchResult($key, $results, $expirationDate));

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
        $searchResult = $this->searchResultsStore->getResult($key);

        if(!$searchResult || !$searchResult->isValidOn($this->now()))
        {
            throw new NotFoundSearchResultException;
        }
        return $searchResult->getResult();
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

        if(!$sharedResult = $this->searchResultsStore->getSharedResult($sharedKey))
        {
            throw new NotFoundSharedSearchResultException;
        }

        return $this->storeResult($sharedResult->getResult());
    }

    /**
     * Test if a shared result is already stored for this set of params
     * @param array $params
     * @return bool
     */
    public function hasSharedResult(array $params)
    {
        $sharedKey = $this->generateSharedKey($params);

        return $this->searchResultsStore->getSharedResult($sharedKey) !== null;
    }

    /**
     * @param $searchResultTTL
     */
    public function setSearchResultTTL($searchResultTTL)
    {
        $this->searchResultTTL = $searchResultTTL;
    }

    /**
     * Generates a shared key based on search parameters
     * @param array $params
     * @return string
     */
    private function generateSharedKey(array $params)
    {
        $this->orderParameters($params);

        return md5(serialize($params) . $this->searchSpace);
    }

    /**
     * Reorders search parameters
     * @param array $params
     */
    private function orderParameters(array &$params)
    {
        ksort($params);
    }

    /**
     * @return \DateTimeImmutable
     */
    private function getSearchResultExpirationDate()
    {
        return $this->now()->modify('+' . $this->searchResultTTL . 'second');
    }

    /**
     * @return \DateTimeImmutable
     */
    protected function now()
    {
        return new \DateTimeImmutable();
    }
}