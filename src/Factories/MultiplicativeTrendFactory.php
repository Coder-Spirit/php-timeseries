<?php


namespace Litipk\TimeSeries\Factories;


use Litipk\TimeSeries\Predictors\MultiplicativeTrendPredictor;


/**
 * Class HoltFactory
 * @package Litipk\TimeSeries
 */
class MultiplicativeTrendFactory extends BasicExponentialSmoothingFactory
{
    /** @var  MultiplicativeTrendFactory */
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
     * @return MultiplicativeTrendPredictor
     */
    protected function getPredictor($alpha, $beta, $level)
    {
        return new MultiplicativeTrendPredictor($alpha, $beta, $level);
    }
}
