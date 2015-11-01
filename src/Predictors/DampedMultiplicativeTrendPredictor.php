<?php


namespace Litipk\TimeSeries\Predictors;


use Litipk\TimeSeries\TimeSeriesException;


/**
 * Class DampedMultiplicativeTrendPredictor
 * @package Litipk\PhpTimeSeries
 */
class DampedMultiplicativeTrendPredictor implements TimeSeriesPredictor
{
    /** @var  double */
    private $alpha;

    /** @var  double */
    private $beta;

    /** @var  double */
    private $theta;

    /** @var  double */
    private $level;

    /** @var  double */
    private $trend;

    /**
     * @param double $alpha Level learning rate
     * @param double $beta  Trend learning rate
     * @param double $theta Damping factor
     * @param double $level
     * @param double $trend
     */
    public function __construct($alpha, $beta, $theta, $level, $trend = 1.0)
    {
        if ($theta <= 0.0 || $theta > 1.0) {
            throw new TimeSeriesException('Theta must be in the (0, 1] interval');
        }

        $this->alpha = (double)min(max(0.0, $alpha), 1.0);
        $this->beta = (double)min(max(0.0, $beta), 1.0);
        $this->theta = (double)$theta;

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
        $m = 0;
        $t = 1;

        for ($i=0; $i<$nStepsToTheFuture; $i++) {
            $t *= $this->theta;
            $m += $t;
        }

        return $this->level * pow($this->trend, $m);
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
     * @return float Damping factor
     */
    public function getTheta()
    {
        return $this->theta;
    }

    /**
     * @param double $dataPoint
     */
    public function ingestDataPoint($dataPoint)
    {
        $tt = pow($this->trend, $this->theta);
        $chgFactor = $this->alpha*($dataPoint - $this->level*$tt);

        $previousLevel = $this->level;

        $this->level = $this->level*$tt + $chgFactor;
        $this->trend = $tt + $this->beta*$chgFactor/$previousLevel;
    }

    /**
     * @param array|\Traversable $dataPoints
     */
    private function ingestData($dataPoints)
    {
        // We don't use $this->ingestDataPoint for performance reasons
        foreach ($dataPoints as $dataPoint) {
            $tt = pow($this->trend, $this->theta);
            $chgFactor = $this->alpha*($dataPoint - $this->level*$tt);

            $previousLevel = $this->level;

            $this->level = $this->level*$tt + $chgFactor;
            $this->trend = $tt + $this->beta*$chgFactor/$previousLevel;
        }
    }
}
