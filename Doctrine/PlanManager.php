<?php

namespace Avro\StripeBundle\Doctrine;

use Avro\StripeBundle\Event\PlanEvent;
use Avro\StripeBundle\Model\PlanInterface;

class PlanManager implements PlanManagerInterface
{
    protected $om;
    protected $dispatcher;
    protected $class;
    protected $repository;

    public function __construct($om, $dispatcher, $class)
    {
        $this->om = $om;
        $this->dispatcher = $dispatcher;
        $this->class = $class;
        $this->repository = $om->getRepository($class);
    }

    public function create()
    {
        $plan = new $this->class();

       	$this->dispatcher->dispatch('avro_stripe.plan.create', new PlanEvent($plan));

        return $plan;
    }

    public function persist(PlanInterface $plan, $andFlush = true)
    {
       	$this->dispatcher->dispatch('avro_stripe.plan.persist', new PlanEvent($plan));
        $this->om->persist($plan);

        if ($andFlush) {
            $this->om->flush();
        }

       	$this->dispatcher->dispatch('avro_stripe.plan.persisted', new PlanEvent($plan));

        return $plan;
    }

    public function update(PlanInterface $plan, $andFlush = true)
    {
       	$this->dispatcher->dispatch('avro_stripe.plan.update', new PlanEvent($plan));
        $this->om->persist($plan);

        if ($andFlush) {
            $this->om->flush();
        }

       	$this->dispatcher->dispatch('avro_stripe.plan.updated', new PlanEvent($plan));

        return $plan;
    }

    public function delete(PlanInterface $plan, $andFlush = true)
    {
        $this->om->remove($plan);

        if ($andFlush) {
            $this->om->flush();
        }

        return true;
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }
}


