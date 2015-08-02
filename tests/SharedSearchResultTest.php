<?php

namespace SelrahcD\SearchCache\Tests;


use SelrahcD\SearchCache\SearchResult;
use SelrahcD\SearchCache\SharedSearchResult;

class SharedSearchResultTest extends \PHPUnit_Framework_TestCase
{

    public function testReturnASearchResult()
    {
        $sharedResult = ['AA', 'BB'];
        $sharedSearchResult = new SharedSearchResult('sharedKey', $sharedResult);

        $searchResult = $sharedSearchResult->createSearchResult('key');

        $this->assertEquals('key', $searchResult->getKey());
        $this->assertEquals($sharedResult, $searchResult->getResult());
    }
}
