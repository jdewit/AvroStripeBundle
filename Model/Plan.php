<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\StripeBundle\Model;

abstract class Plan implements PlanInterface
{
    protected $name;
    protected $amount;
    protected $currency;
    protected $interval;
    protected $isActive = true;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmountInCents()
    {
        return $this->amount * 100;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function setInterval($interval)
    {
        $this->interval = $interval;
        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
