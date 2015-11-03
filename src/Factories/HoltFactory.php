<?php


namespace Litipk\TimeSeries\Factories;


use Litipk\TimeSeries\Predictors\HoltPredictor;


/**
 * Class HoltFactory
 * @package Litipk\TimeSeries
 */
class HoltFactory extends BasicExponentialSmoothingFactory
{
    /** @var  HoltFactory */
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
     * @return HoltPredictor
     */
    protected function getPredictor($alpha, $beta, $level)
    {
        return new HoltPredictor($alpha, $beta, $level);
    }

    public function fastTrain(array &$dataPoints, $nStepsPerParam = 101)
    {
        $minError = +INF;
        $bestPredictor = null;

        // Local variables to avoid too many indirections
        $stepSize = 1./($nStepsPerParam-1);
        $firstLevel = $dataPoints[0];

        $errF = function ($a,$b) { return pow($a-$b, 2); };

        for ($i=0; $i<$nStepsPerParam; $i++) {
            for ($j=0; $j<$nStepsPerParam; $j++) {
                $candidate = $this->getPredictor($i*$stepSize, $j*$stepSize, $firstLevel);
                $candidateError = 0.0;

                foreach($candidate->ingestDataArrayAndPredict($dataPoints, $errF) as $error) {
                    $candidateError += $error;
                }

                if ($candidateError < $minError) {
                    $minError = $candidateError;
                    $bestPredictor = $candidate;
                }
            }
        }

        return $bestPredictor;
    }
}
