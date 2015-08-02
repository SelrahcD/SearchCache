<?php

namespace SelrahcD\SearchCache\Tests\KeyGenerators;


use SelrahcD\SearchCache\KeyGenerators\UniqidKeyGenerator;

class UniqIdKeyTest extends \PHPUnit_Framework_TestCase
{
    private $keyGenerator;

    protected function setUp()
    {
        $this->keyGenerator = new UniqidKeyGenerator();
    }

   public function testShouldReturnAKey()
   {
       $this->assertNotNull($this->keyGenerator->generateKey());
   }

    public function testShouldReturnADifferentKeyEachTime()
    {
        $this->assertNotEquals($this->keyGenerator->generateKey(), $this->keyGenerator->generateKey());
    }
}
