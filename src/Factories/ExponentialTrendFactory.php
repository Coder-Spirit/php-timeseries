<?php


namespace Litipk\TimeSeries\Factories;


use Litipk\TimeSeries\Predictors\TimeSeriesPredictor;
use Litipk\TimeSeries\Predictors\ExponentialTrendPredictor;


/**
 * Class HoltFactory
 * @package Litipk\TimeSeries
 */
class ExponentialTrendFactory extends BasicExponentialSmoothingFactory
{
    /** @var  ExponentialTrendFactory */
    private static $defaultInstance = null;

    /**
     * @param integer $nStepsPerParam
     */
    public function __construct($nStepsPerParam)
    {
        parent::__construct($nStepsPerParam);
    }

    /**
     * @return PredictorFactory
     */
    protected static function getDefaultInstance()
    {
        if (null === static::$defaultInstance) {
            static::$defaultInstance = new static(101);
        }
        return static::$defaultInstance;
    }

    /**
     * @param double $alpha
     * @param double $beta
     * @param double $level
     * @return TimeSeriesPredictor
     */
    protected function getPredictor($alpha, $beta, $level)
    {
        return new ExponentialTrendPredictor($alpha, $beta, $level);
    }
}
