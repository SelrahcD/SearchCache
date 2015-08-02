<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use Predis\Client;
use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SearchResultStores;
use SelrahcD\SearchCache\SharedSearchResult;

final class PredisSearchResultStore implements SearchResultsStore
{
    /**
     * @var Client
     */
    private $client;

    /**
     * PredisSearchResultStore constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function store(SearchResult $searchResult)
    {
        $this->client->sadd($searchResult->getKey(), $searchResult->getResult());
    }

    /**
     * @inheritdoc
     */
    public function storeSharedResult(SharedSearchResult $searchResult)
    {
        $this->client->sadd($searchResult->getKey(), $searchResult->getResult());
    }

    /**
     * @inheritdoc
     */
    public function getResult($key)
    {
        $result = $this->client->smembers($key);

        if(empty($result))
        {
            throw new NotFoundSearchResultException;
        }

        return new SearchResult($key, $result);
    }

    /**
     * @inheritdoc
     */
    public function getSharedResult($key)
    {
        $result = $this->client->smembers($key);

        if(empty($result))
        {
            throw new NotFoundSharedSearchResultException;
        }

        return new SharedSearchResult($key, $result);
    }
}