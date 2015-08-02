<?php

namespace SelrahcD\SearchCache;

final class SharedSearchResult
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var array
     */
    private $result;

    /**
     * SharedSearchResult constructor.
     * @param string $key
     * @param array $result
     */
    public function __construct($key, array $result)
    {
        $this->key = $key;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns a matching search result
     * @param $key
     * @return SearchResult
     */
    public function createSearchResult($key)
    {
        return new SearchResult($key, $this->getResult());
    }

}