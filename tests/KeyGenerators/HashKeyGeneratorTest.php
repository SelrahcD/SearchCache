<?php

namespace SelrahcD\SearchCache\Tests\KeyGenerators;


use SelrahcD\SearchCache\KeyGenerators\HashKeyGenerator;

class HashKeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $keyGenerator;

    protected function setUp()
    {
        $this->keyGenerator = new HashKeyGenerator();
    }

    public function testReturnsSameKeyIfSearchParametersAndResultsAreTheSame()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $key1 = $this->keyGenerator->generateKey($params, $results);
        $key2 = $this->keyGenerator->generateKey($params, $results);

        $this->assertEquals($key1, $key2);
    }

    public function testReturnsDifferentKeyIfSearchParametersArentTheSame()
    {
        $params1 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $params2 = [
            'name' => 'test2',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $key1 = $this->keyGenerator->generateKey($params1, $results);
        $key2 = $this->keyGenerator->generateKey($params2, $results);

        $this->assertNotEquals($key1, $key2);
    }

    public function testReturnsDifferentKeyIfSearchResultArentTheSame()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results1 = [1, 'AA', 3, "HUG76767"];
        $results2 = [1, 'AA'];

        $key1 = $this->keyGenerator->generateKey($params, $results1);
        $key2 = $this->keyGenerator->generateKey($params, $results2);

        $this->assertNotEquals($key1, $key2);
    }

    public function testReturnsDifferentKeyIfSearchResultsArentInTheSameOrder()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results1 = [1, 'AA', 3, "HUG76767"];
        $results2 = [1, 'AA', "HUG76767", 3];

        $key1 = $this->keyGenerator->generateKey($params, $results1);
        $key2 = $this->keyGenerator->generateKey($params, $results2);


        $this->assertNotEquals($key1, $key2);
    }
}
