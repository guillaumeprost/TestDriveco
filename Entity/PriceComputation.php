<?php

namespace Entity;

class PriceComputation
{
    private ?\DateTimeImmutable $from = null;
    private ?\DateTimeImmutable $to = null;

    /** @var PriceRule[] */
    private array $rules = [];

    public function getFrom(): ?\DateTimeImmutable
    {
        return $this->from;
    }

    public function setFrom(?\DateTimeImmutable $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?\DateTimeImmutable
    {
        return $this->to;
    }

    public function setTo(?\DateTimeImmutable $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRule(PriceRule $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }
}
