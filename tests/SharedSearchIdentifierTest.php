<?php

namespace SelrahcD\SearchCache\Tests;

use SelrahcD\SearchCache\SharedSearchIdentifier;

class SharedSearchIdentifierTest extends \PHPUnit_Framework_TestCase
{

    public function testGetKeyReturnsAString()
    {
        $id = new SharedSearchIdentifier(['name' => 'test']);

        $this->assertTrue(is_string($id->getKey()));
    }

    public function testGetKeyReturnsSameResultIfParametersAreTheSameAndNoSearchSpaceProvided()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $id1 = new SharedSearchIdentifier($params);
        $id2 = new SharedSearchIdentifier($params);

        $this->assertEquals($id1->getKey(), $id2->getKey());
    }

    public function testGetKeyReturnsADifferentResultIfParamsAreNotTheSame()
    {
        $params1 = [
            'name' => 'test',
            'age'  => 54,
        ];

        $params2 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $id1 = new SharedSearchIdentifier($params1);
        $id2 = new SharedSearchIdentifier($params2);
        $this->assertNotEquals($id1->getKey(), $id2->getKey());
    }

    public function testOrderOfParametersDoesntInfluenceOverKeyGeneration()
    {
        $params1 = [
            'age'  => 12,
            'name' => 'test',
        ];

        $params2 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $id1 = new SharedSearchIdentifier($params1);
        $id2 = new SharedSearchIdentifier($params2);
        $this->assertEquals($id1->getKey(), $id2->getKey());
    }

    public function testGetKeyReturnsDifferentKeysIfDifferentSearchSpaceAreProvided()
    {
        $params1 = [
            'age'  => 12,
            'name' => 'test',
        ];

        $params2 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $id1 = new SharedSearchIdentifier($params1, 'pony');
        $id2 = new SharedSearchIdentifier($params2, 'cat');
        $this->assertNotEquals($id1->getKey(), $id2->getKey());
    }

    public function testGetKeyReturnsSameKeysIfSameSearchSpaceAreProvided()
    {
        $params1 = [
            'age'  => 12,
            'name' => 'test',
        ];

        $params2 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $id1 = new SharedSearchIdentifier($params1, 'pony');
        $id2 = new SharedSearchIdentifier($params2, 'pony');
        $this->assertEquals($id1->getKey(), $id2->getKey());
    }
}
