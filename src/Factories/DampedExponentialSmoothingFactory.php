<?php


namespace Litipk\TimeSeries\Factories;


use Litipk\TimeSeries\TimeSeriesException;
use Litipk\TimeSeries\Predictors\TimeSeriesPredictor;


/**
 * Class DampedExponentialSmoothingFactory
 * @package Litipk\TimeSeries
 */
abstract class DampedExponentialSmoothingFactory extends PredictorFactory
{
    /** @var  integer */
    private $nStepsPerParam;

    /** @var  integer */
    private $predictionHorizon;

    /**
     * @param integer $nStepsPerParam
     * @param integer $predictionHorizon
     */
    protected function __construct($nStepsPerParam, $predictionHorizon)
    {
        $this->nStepsPerParam = (int)max(2, $nStepsPerParam);
        $this->predictionHorizon = is_infinite($predictionHorizon) ?
            +INF : (int)ceil(max(2, $predictionHorizon));
    }

    /**
     * @param array $dataPoints
     * @return DampedHoltFactory
     */
    public function train(array $dataPoints)
    {
        $dataSetSize = count($dataPoints);
        if ($dataSetSize < 6) {
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
                for ($k=1; $k<$nStepsPerParam; $k+=2) {
                    $candidate = $this->getPredictor($i*$stepSize, $j*$stepSize, $k*$stepSize, $firstLevel);
                    $candidateError = 0.0;

                    for ($u=1; $u<$dataSetSize; $u++) {
                        for ($v=0; $v<$this->predictionHorizon && $u+$v<$dataSetSize; $v++) {
                            $candidateError += pow($candidate->predict($v+1)-$dataPoints[$u+$v], 2);
                        }
                        $candidate->ingestDataPoint($dataPoints[$u]);
                    }

                    if ($candidateError < $minError) {
                        $minError = $candidateError;
                        $bestPredictor = $candidate;
                    }
                }
            }
        }

        return $bestPredictor;
    }

    /**
     * @param double $alpha
     * @param double $beta
     * @param double $theta
     * @param double $level
     * @return TimeSeriesPredictor
     */
    abstract protected function getPredictor($alpha, $beta, $theta, $level);
}
