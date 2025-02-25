<?php

class PriceComputation
{
    private ?\DateTimeImmutable $from = null;
    private ?\DateTimeImmutable $to = null;

    /** @var PriceRule[] */
    private array $rules = [];

    public function from(\DateTimeImmutable $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function to(\DateTimeImmutable $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function addRule(PriceRule $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function run(): float
    {
        if ($this->from === null || $this->to === null) {
            throw new \Exception("dates must be defined");
        }
        if ($this->from >= $this->to) {
            throw new \Exception("start date must be less than end date");
        }

        $totalPrice = 0.0;
        $current = $this->from;

        while ($current < $this->to) {
            $minuteOfDay = ((int)$current->format('G')) * 60 + (int)$current->format('i');

            $applicableRules = array_filter($this->rules, function (PriceRule $rule) use ($current, $minuteOfDay) {
                return $rule->appliesTo($current, $minuteOfDay);
            });

            if (!empty($applicableRules)) {
                usort($applicableRules, function (PriceRule $firstRule, PriceRule $secondRule) {
                    return $secondRule->priority <=> $firstRule->priority;
                });
                $selectedRule = $applicableRules[0];
                $totalPrice += $selectedRule->minutePrice;
            } else {
                // Default behavior
                // $totalPrice += 0;
            }

            $current = $current->modify('+1 minute');
        }

        return $totalPrice;
    }


    public function setDateFromConsole(): void
    {
        echo "Enter start date (YYYY-MM-DD HH:MM): ";
        $fromInput = trim(fgets(STDIN));
        echo "Enter end date (YYYY-MM-DD HH:MM): ";
        $toInput = trim(fgets(STDIN));

        try {
            $this->from = new \DateTimeImmutable($fromInput);
            $this->to = new \DateTimeImmutable($toInput);
        } catch (\Exception $e) {
            echo "Invalid date format. Please use YYYY-MM-DD HH:MM.\n";
            exit(1);
        }
    }
}
