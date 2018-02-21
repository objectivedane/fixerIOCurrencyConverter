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

    public function testResolvedPromise() {
        $mockGuzzleClient = $this->getMockBuilder('Guzzle')
            ->setMethods(['requestAsync'])
            ->getMock();

        $promise = new \GuzzleHttp\Promise\Promise(function() use(&$promise){
            $promise->resolve(new \GuzzleHttp\Psr7\Response(200, [], json_encode(['json'=>'value'])));
        });

        $mockGuzzleClient->method('requestAsync')->willReturn($promise);

        try{
            $this->assertJson('{"json":"value"}',ApiWorker::getXRates($mockGuzzleClient), 'Gave json via resolve and didnt get it from the method');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function testRejectedPromise() {

        $mockGuzzleClient = $this->getMockBuilder('Guzzle')
            ->setMethods(['requestAsync'])
            ->getMock();

        $promise = new \GuzzleHttp\Promise\Promise(function() use(&$promise){
            $promise->reject(new \GuzzleHttp\Exception\RequestException('rejected promise', new \GuzzleHttp\Psr7\Request('method','uri')));
        });

        $mockGuzzleClient->method('requestAsync')->willReturn($promise);

        try {
            ApiWorker::getXRates($mockGuzzleClient);
            } catch (Exception $e) {
            $this->assertTrue(get_class($e) == \GuzzleHttp\Exception\RequestException::class, 'Rejected promise should throw RequestExpection');
        }
    }


    public function testGetXRates()
    {
        $rates = ApiWorker::getXRates();
        $this->assertJson( $rates , 'LIVE API did not return JSON');

        $rates = json_decode($rates, true);
        $this->assertArrayHasKey('GBP', $rates['rates'], 'LIVE - $GBP was not found, the empire was not built on such disregard for authority.');

        $this->assertTrue($rates['base'] === 'EUR', 'LIVE - BASE IS NOT EURO.');
    }

}
