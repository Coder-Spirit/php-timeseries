<?php


namespace Litipk\TimeSeries;


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
        if (!is_integer($nStepsPerParam)) {
            throw new TimeSeriesException('The $nStepsPerParam parameter must be an integer number');
        }

        if ($nStepsPerParam < 1) {
            throw new TimeSeriesException('The $nStepsPerParam parameter must be positive');
        }

        $this->nStepsPerParam = (int)$nStepsPerParam;
    }

    /**
     * @param array $dataPoints
     * @return HoltPredictor
     */
    public function train(array $dataPoints)
    {
        $dataSetSize = count($dataPoints);
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
                    $candidateError += pow($candidate->predict()-$dataPoints[$k], 2);
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
