<?php
/**
 * Created by PhpStorm.
 * User: danestevens
 * Date: 15/02/2018
 * Time: 17:50
 */

namespace BaseTwo;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiWorker
 * Guzzles up the xchange rates from fizxer.io
 * @package BaseTwo
 */
class ApiWorker
{
    const API_URL = 'https://api.fixer.io/latest';
    /**
     * Tries to get the xchange rate from fixer.  Throws exception on fail
     * Returns JSON response which we eventually decode to an assoc array.  So if you need to change this API it must still give us ['date','rates','base']
     * @return ResponseInterface
     * @throws RequestException
     * @param $testGuzzle mixed Mock test class
     */
    static function getXRates( $testGuzzle = false ) {

        // if testGuzzle is given as an argument then use it instead of guzzle
        if( $testGuzzle !== false ) {
            $guzzle = $testGuzzle;
        } else {
            $guzzle = new \GuzzleHttp\Client();
        }

        $promise = $guzzle->requestAsync('GET', self::API_URL, ['connect_timeout' => 3]);

        $promise->then(function(Response $response) use ($promise) {
            //All good
            log_message('info', 'Fixer was called and responded');
             $promise->resolve($response);

        }, function (RequestException $reason) use ($promise) {
            //Not good
            log_message('error', 'Fixer was called and FAILED: ' . $reason->getMessage());
            throw $reason;
        });

        $resp = $promise->wait();
        return $resp->getBody();

    }

}