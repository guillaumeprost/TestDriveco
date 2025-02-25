<?php

class PriceRule
{
    public function __construct(
        private readonly int $weekDayFrom,
        private readonly int $weekDayTo,
        private readonly int $minuteFrom,
        private readonly int $minuteTo,
        public float $minutePrice {
            get {
                return $this->minutePrice;
            }
            set {
                $this->minutePrice = $value;
            }
        },
        public int $priority {
            get {
                return $this->priority;
            }
            set {
                $this->priority = $value;
            }
        }
    )
    {
    }

    public function appliesTo(\DateTimeImmutable $date, int $minuteOfDay): bool
    {
        $day = (int)$date->format('N'); // 1 = lundi, …, 7 = dimanche
        if ($day < $this->weekDayFrom || $day > $this->weekDayTo) {
            return false;
        }
        return ($minuteOfDay >= $this->minuteFrom && $minuteOfDay < $this->minuteTo);
    }
}
