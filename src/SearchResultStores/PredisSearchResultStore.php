<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use Predis\Client;
use SelrahcD\SearchCache\Exceptions\NotFoundSearchResultException;
use SelrahcD\SearchCache\Exceptions\NotFoundSharedSearchResultException;
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
        $result = $this->client->smembers($key);

        if($result === -1)
        {
            throw new NotFoundSearchResultException;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSharedResult($key)
    {
        $result = $this->client->smembers($key);

        if($result === -1)
        {
            throw new NotFoundSharedSearchResultException;
        }

        return $result;
    }
}