<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Framework\Model\Resource\Type\Db\Pdo;

class MysqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Stdlib\String|\PHPUnit_Framework_MockObject_MockObject
     */
    private $string;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTime;

    protected function setUp()
    {
        $this->string = $this->getMock('\Magento\Framework\Stdlib\String');
        $this->dateTime = $this->getMock('\Magento\Framework\Stdlib\DateTime');
    }

    /**
     * @param array $inputConfig
     * @param array $expectedConfig
     *
     * @dataProvider constructorDataProvider
     */
    public function testConstructor(array $inputConfig, array $expectedConfig)
    {
        $object = new Mysql($this->string, $this->dateTime, $inputConfig);
        $this->assertAttributeEquals($expectedConfig, '_connectionConfig', $object);
    }

    /**
     * @return array
     */
    public function constructorDataProvider()
    {
        return [
            'default values' => [
                ['host' => 'localhost'],
                ['host' => 'localhost', 'initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => false],
            ],
            'custom values' => [
                ['host' => 'localhost', 'initStatements' => 'init statement', 'type' => 'type', 'active' => true],
                ['host' => 'localhost', 'initStatements' => 'init statement', 'type' => 'type', 'active' => true],
            ],
            'active string true' => [
                ['host' => 'localhost', 'active' => 'true'],
                ['host' => 'localhost', 'initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => true],
            ],
            'non-active string false' => [
                ['host' => 'localhost', 'active' => 'false'],
                ['host' => 'localhost', 'initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => false],
            ],
            'non-active string 0' => [
                ['host' => 'localhost', 'active' => '0'],
                ['host' => 'localhost', 'initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => false],
            ],
            'non-active bool false' => [
                ['host' => 'localhost', 'active' => false],
                ['host' => 'localhost', 'initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => false],
            ],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage MySQL adapter: Missing required configuration option 'host'
     */
    public function testConstructorException()
    {
        new Mysql($this->string, $this->dateTime, []);
    }

    public function testGetConnectionInactive()
    {
        $config = ['host' => 'localhost', 'active' => false];
        $object = new Mysql($this->string, $this->dateTime, $config);
        $logger = $this->getMockForAbstractClass('Magento\Framework\DB\LoggerInterface');
        $this->assertNull($object->getConnection($logger));
    }
}
