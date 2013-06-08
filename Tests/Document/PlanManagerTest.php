<?php

/**
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Avro\StripeBundle\Tests\Document;

use Avro\StripeBundle\Model\Plan as BasePlan;
use Avro\StripeBundle\Model\PlanInterface;
use Avro\StripeBundle\Doctrine\PlanManager;
use Avro\StripeBundle\Tests\StripeTestCommon;

use Doctrine\Bundle\MongoDBBundle\Tests\TestCase;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\Bundle\MongoDBBundle\Mapping\Driver\XmlDriver;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class PlanManagerTest extends TestCase
{
    /**
     *
     * @var \Vespolina\PlanBundle\Document\PlanManager
     */
    protected $planManager;

    /**
     *
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    public function createPlan()
    {
        $plan = $this->planManager->create();
        $plan->setId('Gold');
        $this->planManager->update($plan);

        return $plan;
    }

    public function testCreatePlan()
    {
        $plan = $this->createPlan();

        $this->assertTrue($plan instanceOf PlanInterface);
        $this->assertTrue($plan->getIsActive());
    }

    public function testDeletePlan()
    {
        $plan = $this->createPlan();

        $plan = $this->planManager->findOneBy(array('id' => 'Gold'));
        $this->planManager->delete($plan);

        $plan = $this->planManager->findOneBy(array('id' => 'Gold', 'isActive' => true));

        $this->assertFalse($plan instanceOf PlanInterface);
    }

    public function testFindPlan()
    {
        $plan = $this->createPlan();

        $plan = $this->planManager->find('Gold');
        $this->assertTrue($plan instanceOf PlanInterface);
    }

    public function testFindOneByPlan()
    {
        $plan = $this->createPlan();

        $plan = $this->planManager->findOneBy(array('id' => 'Gold'));
        $this->assertTrue($plan instanceOf PlanInterface);
    }

    public function testFindAllPlans()
    {
        $plan = $this->createPlan();

        $plans = $this->planManager->findAll();

        $this->assertEquals(1, $plans->count());
    }

    public function testFindActivePlans()
    {
        $plan = $this->createPlan();

        $plans = $this->planManager->findActive();

        $this->assertEquals(1, $plans->count());
    }

    public function testFindDeletedPlans()
    {
        $plan = $this->createPlan();

        $plan = $this->planManager->findOneBy(array('id' => 'Gold'));
        $this->planManager->delete($plan);

        $plans = $this->planManager->findActive();
        $this->assertEquals(0, $plans->count());

        $plans = $this->planManager->findDeleted();
        $this->assertEquals(1, $plans->count());

    }

    public function setup()
    {
        $this->dm = self::createTestDocumentManager();

        $xmlDriver = new XmlDriver(array(realpath(__DIR__.'/../') . '/Resources/config/doctrine' => 'Avro\StripeBundle\Tests\Fixtures\Document'), '.mongodb.xml');

        $this->dm->getConfiguration()->setMetadataDriverImpl($xmlDriver);

        $this->planManager = new PlanManager($this->dm, 'Avro\StripeBundle\Tests\Fixtures\Document\Plan');
    }

    public function tearDown()
    {
        $collections = $this->dm->getDocumentCollections();
        foreach ($collections as $collection) {
            $collection->drop();
        }
    }
}
