<?php
/**
 * Created by PhpStorm.
 * User: danestevens
 * Date: 16/02/2018
 * Time: 08:07
 */

use BaseTwo\LocalWorker;


class LocalWorkerTest extends \PHPUnit\Framework\TestCase
{

    public $controller;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $controller = $this->getMockBuilder('aController')
            ->getMock();

        $controller->cache = $this->getMockBuilder('Cache')
            ->setMethods(['get'])
            ->getMock();

        $controller->cache->file = $this->getMockBuilder('File')
            ->setMethods(['save'])
            ->getMock();

        $controller->load = $this->getMockBuilder('driver')
            ->setMethods(['driver'])
            ->getMock();

        $this->controller = $controller;

    }

    public function test__construct()
    {
        $lw = new LocalWorker( $this->controller );
        $this->assertObjectHasAttribute( 'codeIg', $lw );

    }

    public function testGetRate()
    {
        $this->controller->cache->method('get')->willReturn([ 'USD' => 0.33333333333333]);
        $lw = new LocalWorker( $this->controller );
        $this->assertTrue( is_double( $lw->getRate('USD' ) ), 'Expected float from method' );
        $this->assertTrue( $lw->getRate('USD') === 0.33333333333333, 'Did not get exact rate expected.');

    }

    public function testSetRate()
    {
        $this->controller->cache->file->method('save')->willReturn( false );
        $lw = new LocalWorker( $this->controller );

        try {
            $lw->setRate('USD', '0.33333333');
        } catch (Exception $e) {
            $this->assertTrue( 1 === 1, 'Something went wrong with the Exception thrown by setRate');
        }

    }
}
