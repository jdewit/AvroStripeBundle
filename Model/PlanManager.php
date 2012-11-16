<?php

namespace Avro\StripeBundle\Model;

abstract class PlanManager implements PlanManagerInterface
{
    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function create()
    {
        return new $this->class();
    }
}
