<?php

namespace Avro\StripeBundle\Model;

interface PlanManagerInterface
{
    public function create();

    public function update(PlanInterface $plan, $andFlush = true);

    public function delete(PlanInterface $plan);

    public function find($id);

    public function findBy(array $criteria);

    public function findAll();

    public function findActive();

    public function findDeleted();
}

