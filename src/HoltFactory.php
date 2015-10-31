<?php


namespace Litipk\TimeSeries;


/**
 * Class HoltFactory
 * @package Litipk\TimeSeries
 */
class HoltFactory extends PredictorFactory
{
    /** @var  HoltFactory */
    private static $defaultInstance = null;

    /** @var  integer */
    private $nStepsPerParam;

    /**
     * @param integer $nStepsPerParam
     */
    public function __construct($nStepsPerParam)
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
                $candidate = new HoltPredictor($i*$stepSize, $j*$stepSize, $firstLevel);
                $candidateError = 0.0;

                for ($k=1; $k<$dataSetSize; $k++) {
                    $candidateError += pow($candidate->predict()-$dataPoints[$i], 2);
                    $candidate->ingestDataPoint($dataPoints[$i]);
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
     * @return PredictorFactory
     */
    protected static function getDefaultInstance()
    {
        if (null === static::$defaultInstance) {
            static::$defaultInstance = new static(101);
        }
        return static::$defaultInstance;
    }
}