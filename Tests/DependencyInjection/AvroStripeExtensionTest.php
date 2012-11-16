<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Avro\StripeBundle\DependencyInjection\AvroStripeExtension;
use Symfony\Component\Yaml\Parser;

class AvroStripeExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testStripeLoadThrowsExceptionUnlessSecretKeySet()
    {
        $loader = new AvroStripeExtension();
        $config = $this->getEmptyConfig();
        unset($config['secret_key']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testStripeLoadThrowsExceptionUnlessPublishableKeySet()
    {
        $loader = new AvroStripeExtension();
        $config = $this->getEmptyConfig();
        unset($config['publishable_key']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testStripeLoadThrowsExceptionUnlessClientIdSet()
    {
        $loader = new AvroStripeExtension();
        $config = $this->getEmptyConfig();
        unset($config['client_id']);
        $loader->load(array($config), new ContainerBuilder());
    }



    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
db_driver: mongodb
secret_key: %stripe_secret_key%
publishable_key: %stripe_publishable_key%
client_id: %stripe_client_id%
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }

}
