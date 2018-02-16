<?php
/**
 * Created by PhpStorm.
 * User: danestevens
 * Date: 15/02/2018
 * Time: 18:23
 */

namespace BaseTwo;

/**
 * Class LocalWorker
 * Works with the codeigshiter cache
 * @package BaseTwo
 */
class LocalWorker
{
    private $codeIg;
    const CACHE_NAME = 'b2XRates';
    const TTL = 60*60*24*7;

    public function __construct( object $ciController )
    {
        $this->codeIg = $ciController;
        $this->codeIg->load->driver('cache', ['adapter' => 'file']);
    }

    /**
     * Retrieves rate from cache/
     * @param string $currencyCode
     * @return float
     * @throws \Exception
     */
    public function getRate(string $currencyCode) : float {

        if ( !isset ( $this->codeIg->cache->get(SELF::CACHE_NAME)[$currencyCode] ) ) {
            throw new \Exception('Rate was not found for '. $currencyCode .' .');
        }

        return floatval( $this->codeIg->cache->get( SELF::CACHE_NAME )[ $currencyCode ] );

    }

    /**
     * Stores a rate in the cache
     * @param string $currencyCode
     * @param string $rate
     * @throws \Exception
     */
    public function setRate(string  $currencyCode, string $rate) {

        $cache = $this->codeIg->cache->get(SELF::CACHE_NAME);

        if( !is_array($cache) ) { $cache = [ $currencyCode => $rate ]; } else { $cache[$currencyCode] = $rate; }
        if( ! $this->codeIg->cache->file->save(SELF::CACHE_NAME, $cache, self::TTL) ) { throw new \Exception('Could not save rate ('. $currencyCode . ' ' . $rate .') to cache.'); }

    }

    /**
     * Gets the base currency for this collection of rates
     * @return mixed
     * @throws \Exception
     */
    public function getBase() {

        if ( !isset ( $this->codeIg->cache->get(SELF::CACHE_NAME)['base'] ) ) { throw new \Exception('There is no base currency in the cache');}

        return  $this->codeIg->cache->get( SELF::CACHE_NAME )[ 'base' ];

    }

    /**
     * The last update date
     * @return mixed
     * @throws \Exception
     */
    public function getUpdateDate() {

        if ( !isset ( $this->codeIg->cache->get(SELF::CACHE_NAME)['date'] ) ) { throw new \Exception('There is no update date in the cache');}

        return  $this->codeIg->cache->get( SELF::CACHE_NAME )[ 'date' ];

    }

    /**
     * @param string $baseCurrency
     * @throws \Exception
     */
    public function setBase(string  $baseCurrency) {

        $cache = $this->codeIg->cache->get(SELF::CACHE_NAME);

        if( !is_array($cache) ) { $cache = [ 'base' => $baseCurrency ]; } else { $cache[ 'base' ] = $baseCurrency; }
        if( ! $this->codeIg->cache->file->save(SELF::CACHE_NAME, $cache, self::TTL) ) { throw new \Exception('Could not save base rate ( ' . $baseCurrency . ' ) to cache'); }

    }

    /**
     * @param string $updateDate
     * @throws \Exception
     */
    public function setUpdateDate(string $updateDate) {

        $cache = $this->codeIg->cache->get(SELF::CACHE_NAME);

        if( !is_array($cache) ) { $cache = [ 'date' => $updateDate ]; } else { $cache[ 'date' ] = $updateDate; }
        if( ! $this->codeIg->cache->file->save(SELF::CACHE_NAME, $cache, self::TTL) ) { throw new \Exception('Could not save date (' . $updateDate . ') to cache.'); }

    }



}