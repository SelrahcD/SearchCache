<?php

namespace SelrahcD\SearchCache\Tests;


use SelrahcD\SearchCache\SearchResult;

class SearchResultTest extends \PHPUnit_Framework_TestCase
{

    public function testIsValidOnReturnsTrueIfExpirationDateIsAfterPassedDate()
    {
        $searchResult = new SearchResult('key', ['aa'], new \DateTimeImmutable('1989-01-13 12:00:00'));

        $this->assertTrue($searchResult->isValidOn(new \DateTimeImmutable('1989-01-13 11:59:00')));
    }

    public function testIsValidOnReturnsFalsIfExpirationDateIsBeforePassedDate()
    {
        $searchResult = new SearchResult('key', ['aa'], new \DateTimeImmutable('1989-01-13 12:00:00'));

        $this->assertFalse($searchResult->isValidOn(new \DateTimeImmutable('1989-01-13 12:00:01')));
    }
}
