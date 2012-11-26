<?php

namespace Avro\StripeBundle\Doctrine;

use Avro\StripeBundle\Model\PlanInterface;
use Avro\StripeBundle\Model\PlanManager as BasePlanManager;

class PlanManager extends BasePlanManager
{
    protected $om;
    protected $class;
    protected $repository;

    public function __construct($om, $class)
    {
        $this->om = $om;
        $this->repository = $om->getRepository($class);

        parent::__construct($class);

    }

    public function update(PlanInterface $plan, $andFlush = true)
    {
        $this->om->persist($plan);

        if ($andFlush) {
            $this->om->flush();
        }

        return true;
    }

    public function delete(PlanInterface $plan)
    {
        $plan->setIsActive(false);

        $this->om->persist($plan);
        $this->om->flush();

        return true;
    }

    public function remove(PlanInterface $plan, $andFlush = true)
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

    public function findActive()
    {
        return $this->repository->findBy(array('isActive' => true));
    }

    public function findDeleted()
    {
        return $this->repository->findBy(array('isActive' => false));
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }
}


