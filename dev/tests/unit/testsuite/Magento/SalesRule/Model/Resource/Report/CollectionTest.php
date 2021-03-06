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
namespace Magento\SalesRule\Model\Resource\Report;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Resource\Report\Collection
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fetchStrategy;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;


    protected function setUp()
    {
        $this->entityFactory = $this->getMock(
            'Magento\Core\Model\EntityFactory',
            [],
            [],
            '',
            false
        );

        $this->loggerMock = $this->getMock(
            'Magento\Framework\Logger',
            [],
            [],
            '',
            false
        );

        $this->fetchStrategy = $this->getMock(
            'Magento\Framework\Data\Collection\Db\FetchStrategyInterface',
            [],
            [],
            '',
            false
        );

        $this->eventManager = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            [],
            [],
            '',
            false
        );

        $this->reportResource = $this->getMock(
            'Magento\Sales\Model\Resource\Report',
            ['getReadConnection', 'getMainTable'],
            [],
            '',
            false
        );

        $this->connection = $this->getMock(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['select', 'getDateFormatSql', 'quoteInto'],
            [],
            '',
            false
        );

        $this->selectMock = $this->getMock(
            'Zend_Db_Select',
            ['from', 'where', 'group'],
            [],
            '',
            false
        );

        $this->connection->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->selectMock));

        $this->reportResource->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($this->connection));
        $this->reportResource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('test_main_table'));

        $this->ruleFactory = $this->getMock(
            'Magento\SalesRule\Model\Resource\Report\RuleFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->object = new Collection(
            $this->entityFactory, $this->loggerMock, $this->fetchStrategy,
            $this->eventManager, $this->reportResource, $this->ruleFactory
        );
    }

    public function testAddRuleFilter()
    {
        $this->assertInstanceOf(get_class($this->object), $this->object->addRuleFilter([]));
    }

    public function testApplyAggregatedTableNegativeIsTotals()
    {
        $this->selectMock->expects($this->once())
            ->method('group')
            ->with($this->equalTo([null, 'coupon_code']));
        $this->assertInstanceOf(get_class($this->object), $this->object->loadWithFilter());
    }

    public function testApplyAggregatedTableIsSubTotals()
    {
        $this->selectMock->expects($this->once())
            ->method('group')
            ->with($this->equalTo(null));

        $this->object->isSubTotals(true);
        $this->assertInstanceOf(get_class($this->object), $this->object->loadWithFilter());
    }

    public function testApplyRulesFilterNoRulesIdsFilter()
    {
        $this->ruleFactory->expects($this->never())
            ->method('create');

        $this->assertInstanceOf(get_class($this->object), $this->object->loadWithFilter());
    }

    public function testApplyRulesFilterEmptyRulesList()
    {
        $rulesList = [];
        $ruleMock = $this->getRuleMock();
        $ruleMock->expects($this->once())
            ->method('getUniqRulesNamesList')
            ->will($this->returnValue($rulesList));

        $this->ruleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($ruleMock));

        $ruleFilter = [1,2,3];
        $this->object->addRuleFilter($ruleFilter);
        $this->assertInstanceOf(get_class($this->object), $this->object->loadWithFilter());
    }

    public function testApplyRulesFilterWithRulesList()
    {

        $rulesList = [1 => 'test rule 1', 10 => 'test rule 10', 30 => 'test rule 30'];
        $this->connection->expects($this->at(1))
            ->method('quoteInto')
            ->with($this->equalTo('rule_name = ?'), $this->equalTo($rulesList[1]))
            ->will($this->returnValue('test_1'));
        $this->connection->expects($this->at(2))
            ->method('quoteInto')
            ->with($this->equalTo('rule_name = ?'), $this->equalTo($rulesList[30]))
            ->will($this->returnValue('test_2'));

        $this->selectMock->expects($this->at(3))
            ->method('where')
            ->with($this->equalTo(implode([
                'test_1',
                'test_2'
            ], ' OR ')));

        $ruleMock = $this->getRuleMock();
        $ruleMock->expects($this->once())
            ->method('getUniqRulesNamesList')
            ->will($this->returnValue($rulesList));

        $this->ruleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($ruleMock));

        $ruleFilter = [1,2,30];
        $this->object->addRuleFilter($ruleFilter);
        $this->assertInstanceOf(get_class($this->object), $this->object->loadWithFilter());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRuleMock()
    {
        return $this->getMock(
            'Magento\SalesRule\Model\Resource\Report\Rule',
            ['getUniqRulesNamesList'],
            [],
            '',
            false
        );
    }
}
