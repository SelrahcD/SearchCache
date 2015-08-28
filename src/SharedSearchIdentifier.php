<?php

namespace SelrahcD\SearchCache;

final class SharedSearchIdentifier
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    private $searchSpace;

    /**
     * SharedSearchIdentifier constructor.
     * @param array $params
     * @param string $searchSpace
     */
    public function __construct(array $params, $searchSpace = '')
    {
        $this->orderParameters($params);

        $this->params = $params;
        $this->searchSpace = $searchSpace;
    }

    public function getKey()
    {
        return md5(serialize($this->params) . $this->searchSpace);
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