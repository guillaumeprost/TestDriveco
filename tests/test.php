<?php
require_once 'asserts.php';

echo "Starting tests ..." . PHP_EOL;

//Tests
testOnePriceRule();
testTwoOverlappingPriceRule();
testTwoNotOverlapingPriceRule();
testmultipleDaysPriceRule();
testMultipleDaysThroughWeekPriceRule();
testWrongDates();
testWrongOrderDates();

//Test functions
function testOnePriceRule(): void
{
    try {
        require_once 'PriceRule.php';
        require_once 'PriceComputation.php';

        $computation = new PriceComputation()
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round(120 * 0.24, 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test with one rule');

        echo "Test with one rule succeed" . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}

function testTwoOverlappingPriceRule(): void{
    require_once 'PriceRule.php';
    require_once 'PriceComputation.php';

    try {
        $computation = new PriceComputation()
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 240, 0.24, 0))
            ->addRule(new PriceRule(1, 7, 210, 240, 0.4, 1));

        $expected = round((90 * 0.24) + (30 * 0.4), 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test with two not overlapping prices');

        echo "Test for two not overlapping prices succeed" . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}

function testTwoNotOverlapingPriceRule(): void{
    try {
        $computation = new PriceComputation()
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 180, 0.24, 0))
            ->addRule(new PriceRule(1, 7, 180, 240, 0.4, 1));

        $expected = round((60 * 0.24) + (60 * 0.4), 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test with two not overlaping prices');

        echo "Test for two overlapping prices succeed" . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}

function testMultipleDaysPriceRule(): void{
    require_once 'PriceRule.php';
    require_once 'PriceComputation.php';

    try {
        $computation = new PriceComputation()
            ->setFrom(new DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new DateTimeImmutable('2024/09/06 2am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round((1440 * 4) * 0.24, 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test multiple days price');

        echo "Test for multiple days succeed" . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

}

function testMultipleDaysThroughWeekPriceRule(): void{
    require_once 'PriceRule.php';
    require_once 'PriceComputation.php';

    try {
        $rule = new PriceRule(1, 7, 0, 1440, 0.5, 0);

        $computation = new PriceComputation()
            ->setFrom(new DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new DateTimeImmutable('2024/09/09 2am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0))
            ->addRule(new PriceRule(6, 7, 0, 1440, 0.18, 99))
            ;

        $expected = round(((1440 * 5) * 0.24) + ((1440 * 2) * 0.18), 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test multiple days throught week price');

        echo "Test for multiple days throught week succeed" . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}

function testWrongDates(): void{
    try {
        require_once 'PriceRule.php';
        require_once 'PriceComputation.php';

        $computation = new PriceComputation()
            ->setFrom(new \DateTimeImmutable('2024/19/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round(120 * 0.24, 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test with wrong dates');

        echo "Test with wrong dates failed succeed" . PHP_EOL;
    } catch (Exception $e) {
        if ($e instanceof DateMalformedStringException){
            echo "Test with wrong dates succeed" . PHP_EOL;
        }
    }
}

function testWrongOrderDates(): void{
    try {
        require_once 'PriceRule.php';
        require_once 'PriceComputation.php';

        $computation = new PriceComputation()
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/01 4am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round(120 * 0.24, 2);
        $result = $computation->run();

        assertEqual($expected, $result, 'Test with wrong dates order');

        echo "Test with wrong dates order failed succeed" . PHP_EOL;
    } catch (Exception $e) {
        if ($e->getMessage() === 'start date must be less than end date'){
            echo "Test with wrong dates order succeed" . PHP_EOL;
        }
    }
}