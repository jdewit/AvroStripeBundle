<?php
//
//namespace Avro\CustomerBundle\Tests\Doctrine;
//
//use Avro\StripeBundle\Manager\CustomerManager;
//
//class CustomerManagerTest extends \PHPUnit_Framework_TestCase
//{
//    protected $customerManager;
//    protected $om;
//    protected $repository;
//
//    public function setUp()
//    {
//        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
//        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContext');
//        $userManager = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
//
//        $this->customerManager = $this->createCustomerManager($request, $context, $userManager);
//    }
//
//    public function testCreate()
//    {
//        $this->customerManager->create();
//    }
//
//    public function createCustomerManager($request, $context, $userManager)
//    {
//        return new CustomerManager($request, $context, $userManager, null, false);
//    }
//}
