<?php

namespace spec\Avro\StripeBundle\Doctrine;

use PHPSpec2\ObjectBehavior;

/**
 * Started playing with phpSpec, much to learn....
 */
class PlanManager extends ObjectBehavior
{
    /**
     * @param $om Doctrine\Common\Persistence\ObjectManager
     * @param $class Avro\Model\PlanInterface
     */
    function let($om, $class)
    {
        $this->beConstructedWith($om, $class);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Avro\StripeBundle\Doctrine\PlanManager');
    }

    /**
     * @param $plan Application\StripeBundle\Document\Plan
     */
    function it_should_create_a_plan($plan)
    {
        $this->create()->shouldReturn($plan);
    }

    /**
     * @param $plan Avro\StripeBundle\Model\PlanInterface
     */
    function it_should_update_a_plan($om, $plan)
    {
        $om->persist($plan)->shouldBeCalled()->willReturn($plan);
        $om->flush()->shouldBeCalled()->willReturn(true);

        $this->update($plan)->shouldReturn(true);
    }

    /**
     * @param $plan Avro\StripeBundle\Model\PlanInterface
     */
    function it_should_delete_a_plan($om, $plan)
    {
        $plan->setIsDeleted(true)->shouldBeCalled();
        $om->persist($plan)->shouldBeCalled()->willReturn($plan);
        $om->flush()->shouldBeCalled()->willReturn(true);

        $this->delete($plan)->shouldReturn(true);
    }

    /**
     * @param $plan Avro\StripeBundle\Model\PlanInterface
     * @param Prophet $repository mock of Doctrine\ORM\EntityRepository
     */
    function it_should_find_a_plan($om, $plan, $repository, $class)
    {
        $om->getRepository($class)->willReturn($repository);
       // ->find(1)->willReturn($plan);

        $this->find(1)->shouldReturn($plan);
    }

}
