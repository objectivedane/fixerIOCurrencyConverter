<?php
/**
 * Created by PhpStorm.
 * User: danestevens
 * Date: 16/02/2018
 * Time: 10:31
 */

use BaseTwo\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{

    private $controller;

    public function setup() {

        $controller = $this->getMockBuilder('aController')
            ->getMock();

        $controller->cache = $this->getMockBuilder('Cache')
            ->setMethods(['get'])
            ->getMock();

        $controller->cache->file = $this->getMockBuilder('File')
            ->setMethods(['set'])
            ->getMock();

        $this->controller = $controller;

    }

    public function testConvertFromTo()
    {

    }

    public function testGetLastUpdateTime()
    {

    }

    public function testUpdateRate()
    {
        $currency = new Currency( $this->controller );
        $currency->updateRate();
        $this->assertTrue( $currency->getLastUpdateTime() );
    }
}
