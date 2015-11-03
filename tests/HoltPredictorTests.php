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

    public function testIngestDataPoint()
    {
        $predictor = new \Litipk\TimeSeries\Predictors\HoltPredictor(0.5, 0.75, 1.0, 1.5);

        $predictor->ingestDataPoint(3.0);

        $this->assertGreaterThan(2.5, $predictor->getLevel());
        $this->assertLessThan(3.0, $predictor->getLevel());

        $this->assertGreaterThan(1.5, $predictor->getTrend());
        $this->assertLessThan(2.0, $predictor->getTrend());
    }

    public function testIngestDataArray_WithOnlyOneItem()
    {
        $predictor = new \Litipk\TimeSeries\Predictors\HoltPredictor(0.5, 0.75, 1.0, 1.5);

        $dataArray = [3.0];
        $predictor->ingestDataArray($dataArray);

        $this->assertGreaterThan(2.5, $predictor->getLevel());
        $this->assertLessThan(3.0, $predictor->getLevel());

        $this->assertGreaterThan(1.5, $predictor->getTrend());
        $this->assertLessThan(2.0, $predictor->getTrend());
    }

    public function testIngestDataArray_WithTwoItems()
    {
        $predictor = new \Litipk\TimeSeries\Predictors\HoltPredictor(0.5, 0.75, 1.0, 1.5);

        $dataArray = [3.0, 4.0];
        $predictor->ingestDataArray($dataArray);

        $this->assertGreaterThan(4.0, $predictor->getLevel());
        $this->assertLessThan(5.0, $predictor->getLevel());
    }

    public function testIngestDataTraversable_WithTwoItems()
    {
        $predictor = new \Litipk\TimeSeries\Predictors\HoltPredictor(0.5, 0.75, 1.0, 1.5);

        $f = function () {
            yield 3; yield 4;
        };
        $g = $f();

        $predictor->ingestDataTraversable($g);

        $this->assertGreaterThan(4.0, $predictor->getLevel());
        $this->assertLessThan(5.0, $predictor->getLevel());
    }
}
