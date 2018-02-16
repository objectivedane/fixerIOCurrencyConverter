<?php
/**
 * Created by PhpStorm.
 * User: danestevens
 * Date: 16/02/2018
 * Time: 10:04
 */

use BaseTwo\ApiWorker;
use PHPUnit\Framework\TestCase;

class ApiWorkerTest extends TestCase
{
    public function testGetXRates()
    {
        $rates = ApiWorker::getXRates();
        $this->assertJson( $rates , 'API did not return JSON');

        $rates = json_decode($rates, true);
        $this->assertArrayHasKey('GBP', $rates['rates'], '$GBP was not found, the empire was not built on such disregard for authority.');

        $this->assertTrue($rates['base'] === 'EUR', 'BASE IS NOT EURO.');
    }

}
