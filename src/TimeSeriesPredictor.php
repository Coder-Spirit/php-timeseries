<?php


namespace Litipk\TimeSeries;


/**
 * Interface TimeSeriesPredictor
 * @package Litipk\TimeSeries
 */
interface TimeSeriesPredictor
{
    /**
     * @param double $dataPoint
     */
    public function ingestDataPoint($dataPoint);

    /**
     * @param (null|double)[] $dataPoints
     */
    public function ingestDataArray(array $dataPoints);

    /**
     * @param \Traversable $dataPoints
     */
    public function ingestDataTraversable(\Traversable $dataPoints);

    /**
     * @param integer $nStepsToTheFuture
     * @return double
     */
    public function predict($nStepsToTheFuture = 1);
}
