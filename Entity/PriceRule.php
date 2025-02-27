<?php

namespace Entity;

class PriceRule
{
    public function __construct(
        private readonly int  $weekDayFrom,
        private readonly int  $weekDayTo,
        private readonly int  $minuteFrom,
        private readonly int  $minuteTo,
        public readonly float $minutePrice,
        public readonly int   $priority,
        private int           $id = 0,
    )
    {
        $this->id = random_int(0, 999);
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function appliesTo(\DateTimeImmutable $date, int $minuteOfDay): bool
    {
        $day = (int)$date->format('N');
        if ($day < $this->weekDayFrom || $day > $this->weekDayTo) {
            return false;
        }
        return ($minuteOfDay >= $this->minuteFrom && $minuteOfDay < $this->minuteTo);
    }
}
