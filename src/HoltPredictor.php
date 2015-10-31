<?php


namespace Litipk\TimeSeries;


/**
 * Class HoltPredictor
 * @package Litipk\PhpTimeSeries
 */
class HoltPredictor implements TimeSeriesPredictor
{
    /** @var  double */
    private $alpha;

    /** @var  double */
    private $beta;

    /** @var  double */
    private $level;

    /** @var  double */
    private $trend;

    /**
     * @param double $alpha Level learning rate
     * @param double $beta  Trend learning rate
     * @param double $level
     * @param double $trend
     */
    public function __construct($alpha, $beta, $level, $trend = 0.0)
    {
        if (!is_double($alpha) || !is_double($beta) || !is_double($level) || !is_double($trend)) {
            throw new TimeSeriesException('Invalid parameter types');
        }

        $this->alpha = (double)$alpha;
        $this->beta = (double)$beta;

        $this->level = (double)$level;
        $this->trend = (double)$trend;
    }


    /**
     * @param (null|double)[] $dataPoints
     */
    public function ingestDataArray(array $dataPoints)
    {
        $this->ingestData($dataPoints);
    }

    /**
     * @param \Traversable $dataPoints
     */
    public function ingestDataTraversable(\Traversable $dataPoints)
    {
        $this->ingestData($dataPoints);
    }

    /**
     * @param integer $nStepsToTheFuture
     * @return double
     */
    public function predict($nStepsToTheFuture = 1)
    {
        return $this->level + $nStepsToTheFuture*$this->trend;
    }

    /**
     * @return float
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return float
     */
    public function getTrend()
    {
        return $this->trend;
    }

    /**
     * @return float Level learning rate
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * @return float Trend learning rate
     */
    public function getBeta()
    {
        return $this->beta;
    }

    /**
     * @param double $dataPoint
     */
    public function ingestDataPoint($dataPoint)
    {
        $chgFactor = $this->alpha*($dataPoint - $this->level - $this->trend);
        $this->level = $this->level + $this->trend + $chgFactor;
        $this->trend = $this->trend + $this->beta*$chgFactor;
    }

    /**
     * @param array|\Traversable $dataPoints
     */
    private function ingestData($dataPoints)
    {
        // We don't use $this->ingestDataPoint for performance reasons
        foreach ($dataPoints as $dataPoint) {
            $chgFactor = $this->alpha*($dataPoint - $this->level - $this->trend);
            $this->level = $this->level + $this->trend + $chgFactor;
            $this->trend = $this->trend + $this->beta*$chgFactor;
        }
    }
}
