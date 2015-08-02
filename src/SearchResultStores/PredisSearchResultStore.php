<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use Predis\Client;
use SelrahcD\SearchCache\SearchResultStores;

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
    public function store($key, array $results)
    {
        $this->client->sadd($key, $results);
    }

    /**
     * @inheritdoc
     */
    public function storeSharedResult($key, array $results)
    {
        $this->client->sadd($key, $results);
    }

    /**
     * @inheritdoc
     */
    public function getResult($key)
    {
        return $this->client->smembers($key);
    }

    /**
     * @inheritdoc
     */
    public function getSharedResult($key)
    {
        return $this->client->smembers($key);
    }
}