<?php

namespace Avro\StripeBundle\Doctrine;

use Avro\StripeBundle\Model\PlanInterface;

interface PlanManagerInterface
{
    public function create();

    public function persist(PlanInterface $plan, $andFlush = true);

    public function update(PlanInterface $plan, $andFlush = true);

    public function delete(PlanInterface $plan);

    public function find($id);

    public function findBy(array $criteria);

    public function findAll();
}
