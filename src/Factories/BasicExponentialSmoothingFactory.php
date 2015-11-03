<?php


namespace Litipk\TimeSeries\Factories;


use Litipk\TimeSeries\Predictors\TimeSeriesPredictor;
use Litipk\TimeSeries\TimeSeriesException;


/**
 * Class HoltFactory
 * @package Litipk\TimeSeries
 */
abstract class BasicExponentialSmoothingFactory extends PredictorFactory
{
    /** @var  integer */
    private $nStepsPerParam;

    /**
     * @param integer $nStepsPerParam
     */
    protected function __construct($nStepsPerParam)
    {
        $this->nStepsPerParam = (int)max(2, $nStepsPerParam);
    }

    /**
     * @param array $dataPoints
     * @return TimeSeriesPredictor
     */
    public function train(array &$dataPoints)
    {
        $dataSetSize = \count($dataPoints);
        if ($dataSetSize < 4) {
            throw new TimeSeriesException('Insufficient number of data points');
        }

        $minError = +INF;
        $bestPredictor = null;

        // Local variables to avoid too many indirections
        $nStepsPerParam = $this->nStepsPerParam;
        $stepSize = 1./($nStepsPerParam-1);
        $firstLevel = $dataPoints[0];

        for ($i=0; $i<$nStepsPerParam; $i++) {
            for ($j=0; $j<$nStepsPerParam; $j++) {
                $candidate = $this->getPredictor($i*$stepSize, $j*$stepSize, $firstLevel);
                $candidateError = 0.0;

                for ($k=1; $k<$dataSetSize; $k++) {
                    $candidateError += \pow($candidate->predict()-$dataPoints[$k], 2);
                    $candidate->ingestDataPoint($dataPoints[$k]);
                }

                if ($candidateError < $minError) {
                    $minError = $candidateError;
                    $bestPredictor = $candidate;
                }
            }
        }

        return $bestPredictor;
    }

    /**
     * @param double $alpha
     * @param double $beta
     * @param double $level
     * @return TimeSeriesPredictor
     */
    protected abstract function getPredictor($alpha, $beta, $level);
}
