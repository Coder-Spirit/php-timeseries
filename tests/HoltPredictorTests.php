<?php


class HoltPredictorTests extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $predictor = new \Litipk\TimeSeries\Predictors\HoltPredictor(0.5, 0.75, 1.0, 1.5);

        $this->assertEquals(0.5, $predictor->getAlpha());
        $this->assertEquals(0.75, $predictor->getBeta());
        $this->assertEquals(1.0, $predictor->getLevel());
        $this->assertEquals(1.5, $predictor->getTrend());
    }

    public function testPredict()
    {
        $predictor = new \Litipk\TimeSeries\Predictors\HoltPredictor(0.5, 0.75, 1.0, 1.5);

        $this->assertEquals(2.5, $predictor->predict());
        $this->assertEquals(2.5, $predictor->predict(1));
        $this->assertEquals(4.0, $predictor->predict(2));
    }
}
