<?php

namespace SelrahcD\SearchCache;

final class SearchResult
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
     * @var \DateTimeImmutable
     */
    private $expirationDate;

    /**
     * SearchResult constructor.
     * @param string $key
     * @param array $result
     * @param \DateTimeImmutable $expirationDate
     */
    public function __construct($key, array $result, \DateTimeImmutable $expirationDate)
    {
        $this->key = $key;
        $this->result = $result;
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTimeImmutable $testDate
     * @return bool
     */
    public function isValidOn(\DateTimeImmutable $testDate)
    {
        return $this->expirationDate > $testDate;
    }
}