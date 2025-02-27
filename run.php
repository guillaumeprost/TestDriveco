<?php

require_once 'PriceRule.php';
require_once 'PriceComputation.php';
require_once 'Services/CalculatePrices.php';

$computation = new PriceComputation();
$computation
    ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
    ->setTo(new \DateTimeImmutable('2024/09/30 4am'))
    ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0))
    ->addRule(new PriceRule(1, 7, 480, 1080, 0.4, 1))
    ->addRule(new PriceRule(6, 7, 0, 1440, 0.18, 99))
;

//$computation->setDateFromConsole();
$calculationService = new Services\CalculatePrices();
$price = $calculationService->run($computation);

echo sprintf("Le prix total pour votre recharge sera de %s€ \n", $price);
exit(0);
