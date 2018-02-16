<?php
/**
 * Created by PhpStorm.
 * User: danestevens
 * Date: 15/02/2018
 * Time: 17:47
 */

namespace BaseTwo;


/**
 * Class Currency
 * We're catching errors here and returning false, with a log being made if enabled.  This allows the dev to handle no xchg rate gracefully.
 * @package BaseTwo
 */
class Currency
{
    private $codeIg;
    private $localWorker;

    /**
     * Currency constructor.
     * Constructs a localworker and injects it with Controller instance from get_instance()
     * @param null $codeIgForTest
     * @throws \Exception
     */
    public function __construct( $codeIgForTest = null )
    {
        if( !$codeIgForTest && !is_subclass_of($this->codeIg =& get_instance(), 'CI_Controller') ) {
            log_message('error', 'Currency class did not get CI_Controller from get_instance function.');
            throw new \Exception('FATAL: Did not get CI Controller from get_instance()');
        }

        $codeIgInstance = ( $codeIgForTest ) ?: $this->codeIg;
        $this->localWorker = new LocalWorker( $codeIgInstance );

    }


    /**
     * Conversion method.  This will return false when forex is unavailable to allow dev to handle gracefully.
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return bool|float|int
     */
    public function convertFromTo(string $from, string $to, float $amount)  {

        if( strlen($from) != 3 || strlen($to) != 3){
            log_message('error', 'The currency code was of an incorrect length.  Arguments were: ' . $from . ' ' . $to);
            return false;
        }

        // If there is no data in the cache, or it is older than a day attempt to get fresh rates and update the cache.
        if ( ! $this->getLastUpdateTime() || $this->getLastUpdateTime() != date('Y-m-d')) {
            if(! $this->getLastUpdateTime() ) {
                log_message('error', 'Could not get rates, and none exist in the cache.  Forex is unavailable. Currency->convertFromTo().');
                return false;
            }
        }


        try {
            // Conversion code //

            $base = $this->localWorker->getBase();
            if( $from === $base ){
                $oneBaseIsxFrom = 1;
            } else {
                $oneBaseIsxFrom = $this->localWorker->getRate($from);
            }

            if( $to === $base) {
                $oneBaseIsxTo = 1;
            } else {
                $oneBaseIsxTo = $this->localWorker->getRate($to);
            }

            $fromToBase = 1 / $oneBaseIsxFrom;

            $fromInBase = $amount * $fromToBase;
            $convertedRate = $fromInBase * $oneBaseIsxTo;

            return $convertedRate;

            // // //

        } catch(\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }


    /**
     * Get the last updated entry date from the cache
     * @return bool|mixed
     */
    public function getLastUpdateTime() {
        try {
            return $this->localWorker->getUpdateDate();
        } catch (\Exception $e) {
            log_message('debug', $e->getMessage());
            return false;
        }
    }


    /**
     * Updates the cache with new rates.
     * @return bool
     */
    public function updateRate() {

        try {
            $rateArrayFromApi = json_decode( ApiWorker::getXRates(), true );

            $localWorker = $this->localWorker;
            foreach( $rateArrayFromApi['rates'] as $code => $rate) {
                $localWorker->setRate($rate, $code);
            }
            $localWorker->setBase( $rateArrayFromApi['base'] );
            $localWorker->setUpdateDate( $rateArrayFromApi['date'] );

        } catch (\Exception $e ){
            log_message('error', 'Currency->updateRate() -- ' . $e->getMessage());
            return false;
        }

    }

}