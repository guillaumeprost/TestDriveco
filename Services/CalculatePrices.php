<?php

namespace Services;

require_once 'Entity/PriceRule.php';
require_once 'Entity/PriceComputation.php';

use Entity\PriceComputation;
use Entity\PriceRule;

class CalculatePrices
{
    public function run(PriceComputation $priceComputation): float
    {
        if ($priceComputation->getFrom() === null || $priceComputation->getTo() === null) {
            throw new \Exception("dates must be defined");
        }
        if ($priceComputation->getFrom() >= $priceComputation->getTo()) {
            throw new \Exception("start date must be less than end date");
        }

        $totalPrice = 0.0;
        $current = $priceComputation->getFrom();
        $usedRules = [];

        while ($current < $priceComputation->getTo()) {
            $minuteOfDay = ((int)$current->format('G')) * 60 + (int)$current->format('i');

            $applicableRules = array_filter($priceComputation->getRules(), function (PriceRule $rule) use ($current, $minuteOfDay) {
                return $rule->appliesTo($current, $minuteOfDay);
            });

            if (!empty($applicableRules)) {
                usort($applicableRules, function (PriceRule $firstRule, PriceRule $secondRule) {
                    return $secondRule->priority <=> $firstRule->priority;
                });
                $selectedRule = $applicableRules[0];
                $usedRules[$selectedRule->getId()]['price'] = $applicableRules[0];
                $usedRules[$selectedRule->getId()]['count'] = ($usedRules[$selectedRule->getId()]['count'] ?? 0) + 1;
                $totalPrice += $selectedRule->minutePrice;
            } else {
                // Default behavior
                // $totalPrice += 0;
            }

            $current = $current->modify('+1 minute');
        }

        $this->displayDetails($usedRules);

        return round($totalPrice, 2);
    }


    public function setDateFromConsole(PriceComputation $priceComputation): void
    {
        echo "Enter start date (YYYY-MM-DD HH:MM): ";
        $fromInput = trim(fgets(STDIN));
        echo "Enter end date (YYYY-MM-DD HH:MM): ";
        $toInput = trim(fgets(STDIN));

        try {
            $priceComputation->setFrom(new \DateTimeImmutable($fromInput));
            $priceComputation->setTo(new \DateTimeImmutable($toInput));
        } catch (\Exception $e) {
            echo "Invalid date format. Please use YYYY-MM-DD HH:MM." . PHP_EOL;
            exit(1);
        }
    }

    public function displayDetails(array $arrayRules): void
    {
        echo "Détail du paiement: " . PHP_EOL;
        foreach ($arrayRules as $data) {
            echo sprintf("%s minutes at %s €", $data['count'], $data['price']->minutePrice) . PHP_EOL;
        }
    }
}