<?php
//
//namespace Avro\StripeBundle\Tests\Manager;
//
//use Avro\StripeBundle\Manager\PlanManager;
//use Avro\StripeBundle\Model\Plan;
//
//class PlanManagerTest extends \PHPUnit_Framework_TestCase
//{
//    const PLAN_CLASS = 'Avro\StripeBundle\Tests\Manager\DummyPlan';
//
//    protected $planManager;
//    protected $om;
//    protected $repository;
//
//    public function setUp()
//    {
//        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
//            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
//        }
//
//        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
//        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
//        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
//        //$this->secretKey = $this->container->getParameter('avro_stripe.secret_key');
//        $this->secretKey = '909';
//
//        $this->om->expects($this->any())
//            ->method('getRepository')
//            ->with($this->equalTo(static::PLAN_CLASS))
//            ->will($this->returnValue($this->repository));
//        $this->om->expects($this->any())
//            ->method('getClassMetadata')
//            ->with($this->equalTo(static::PLAN_CLASS))
//            ->will($this->returnValue($class));
//        $class->expects($this->any())
//            ->method('getName')
//            ->will($this->returnValue(static::PLAN_CLASS));
//
//        $this->planManager = $this->createPlanManager($this->om, static::PLAN_CLASS);
//    }
//
//    public function testCreatePlan()
//    {
//        $plan = $this->getManager()->createPlan(Plan::ROLE_CUSTOMER, Plan::INDIVIDUAL);
//        $this->assertTrue($plan instanceOf Plan);
//        $this->assertEquals(Plan::INDIVIDUAL, $plan->getType());
//        $this->assertContains(Plan::ROLE_CUSTOMER, $plan->getRoles());
//
//        $plan = $this->planManager->create();
//
//        $this->assertEquals($plan, $this->getPlan());
//    }
//
//    public function testUpdatePlan()
//    {
//        $plan = $this->getPlan();
//        $this->om->expects($this->once())->method('persist')->with($this->equalTo($plan));
//        $this->om->expects($this->once())->method('flush');
//
//        $this->planManager->update($plan);
//    }
//
//    public function testDeletePlan()
//    {
//        $plan = $this->getPlan();
//        $this->om->expects($this->once())->method('persist')->with($this->equalTo($plan));
//        $this->om->expects($this->once())->method('flush');
//
//        $this->planManager->delete($plan);
//    }
//
//    public function testGetClass()
//    {
//        $this->assertEquals(static::PLAN_CLASS, $this->planManager->getClass());
//    }
//
//    public function testFindBy()
//    {
//        $crit = array("foo" => "bar");
//        $this->repository->expects($this->once())->method('findBy')->with($this->equalTo($crit))->will($this->returnValue(array()));
//
//        $this->planManager->findBy($crit);
//    }
//
//    public function testFindActive()
//    {
//        $this->repository->expects($this->once())->method('findBy')->with($this->equalTo(array('isActive' => true)))->will($this->returnValue(array()));
//
//        $this->planManager->findActive();
//    }
//
//    public function testFindDeleted()
//    {
//        $this->repository->expects($this->once())->method('findBy')->with($this->equalTo(array('isActive' => false)))->will($this->returnValue(array()));
//
//        $this->planManager->findDeleted();
//    }
//
//
//    protected function createPlanManager($objectManager, $planClass)
//    {
//        return new PlanManager($objectManager, $planClass);
//    }
//
//    protected function getPlan()
//    {
//        $planClass = static::PLAN_CLASS;
//
//        return new $planClass();
//    }
//}
//
//class DummyPlan extends Plan
//{
//}
