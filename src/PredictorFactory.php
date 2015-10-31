<?php


namespace Litipk\TimeSeries;


/**
 * Class TimeSeriesFactory
 * @package Litipk\TimeSeries
 */
abstract class PredictorFactory
{
    /**
     * @param array $dataPoints
     * @return TimeSeriesPredictor
     */
    public abstract function train(array $dataPoints);


    /**
     * @return PredictorFactory
     */
    protected static function getDefaultInstance()
    {
        throw new \RuntimeException('Not implemented. Should be implemented by the subclasses');
    }

    /**
     * @param array $dataPoints
     * @return TimeSeriesPredictor
     */
    public static function trainPredictor(array $dataPoints)
    {
        return static::getDefaultInstance()->train($dataPoints);
    }
}
