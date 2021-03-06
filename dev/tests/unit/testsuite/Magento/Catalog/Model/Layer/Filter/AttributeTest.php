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

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class AttributeTest extends \PHPUnit_Framework_TestCase
{

    /** @var  \Magento\Catalog\Model\Resource\Layer\Filter\Attribute|MockObject */
    private $filterAttribute;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Attribute
     */
    private $target;

    /** @var  \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend|MockObject */
    private $frontend;

    /** @var  \Magento\Catalog\Model\Layer\State|MockObject */
    private $state;

    /** @var  \Magento\Eav\Model\Entity\Attribute|MockObject */
    private $attribute;

    /** @var \Magento\Framework\App\RequestInterface|MockObject */
    private $request;

    /** @var  \Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory|MockObject */
    private $filterAttributeFactory;

    /** @var  \Magento\Catalog\Model\Layer\Filter\ItemFactory|MockObject */
    private $filterItemFactory;

    /** @var  \Magento\Framework\StoreManagerInterface|MockObject */
    private $storeManager;

    /** @var  \Magento\Catalog\Model\Layer|MockObject */
    private $layer;

    /** @var  \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder|MockObject */
    private $itemDataBuilder;

    protected function setUp()
    {

        /** @var \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory */
        $this->filterItemFactory = $this->getMockBuilder('\Magento\Catalog\Model\Layer\Filter\ItemFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        /** @var \Magento\Framework\StoreManagerInterface $storeManager */
        $this->storeManager = $this->getMockBuilder('\Magento\Framework\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass();
        /** @var \Magento\Catalog\Model\Layer $layer */
        $this->layer = $this->getMockBuilder('\Magento\Catalog\Model\Layer')
            ->disableOriginalConstructor()
            ->setMethods(['getState'])
            ->getMock();
        /** @var \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder */
        $this->itemDataBuilder = $this->getMockBuilder('\Magento\Catalog\Model\Layer\Filter\Item\DataBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['addItemData', 'build'])
            ->getMock();

        $this->filterAttribute = $this->getMockBuilder('\Magento\Catalog\Model\Resource\Layer\Filter\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getCount', 'applyFilterToCollection'])
            ->getMock();

        $this->filterAttribute->expects($this->any())
            ->method('applyFilterToCollection')
            ->will($this->returnSelf());

        $this->filterAttributeFactory = $this->getMockBuilder(
            '\Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory'
        )
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->filterAttributeFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->filterAttribute));

        $this->state = $this->getMockBuilder('\Magento\Catalog\Model\Layer\State')
            ->disableOriginalConstructor()
            ->setMethods(['addFilter'])
            ->getMock();
        $this->layer->expects($this->any())
            ->method('getState')
            ->will($this->returnValue($this->state));

        $this->frontend = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend')
            ->disableOriginalConstructor()
            ->setMethods(['getOption', 'getSelectOptions'])
            ->getMock();
        $this->attribute = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getAttributeCode', 'getFrontend', 'getIsFilterable'])
            ->getMock();
        $this->attribute->expects($this->atLeastOnce())
            ->method('getFrontend')
            ->will($this->returnValue($this->frontend));

        $this->request = $this->getMockBuilder('\Magento\Framework\App\RequestInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass();

        $stripTagsFilter = $this->getMockBuilder('\Magento\Framework\Filter\StripTags')
            ->disableOriginalConstructor()
            ->setMethods(['filter'])
            ->getMock();
        $stripTagsFilter->expects($this->any())
            ->method('filter')
            ->will($this->returnArgument(0));

        $string = $this->getMockBuilder('\Magento\Framework\Stdlib\String')
            ->disableOriginalConstructor()
            ->setMethods(['strlen'])
            ->getMock();
        $string->expects($this->any())
            ->method('strlen')
            ->will(
                $this->returnCallback(
                    function ($value) {
                        return strlen($value);
                    }
                )
            );

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->target = $objectManagerHelper->getObject(
            'Magento\Catalog\Model\Layer\Filter\Attribute',
            [
                'filterItemFactory' => $this->filterItemFactory,
                'storeManager' => $this->storeManager,
                'layer' => $this->layer,
                'itemDataBuilder' => $this->itemDataBuilder,
                'filterAttributeFactory' => $this->filterAttributeFactory,
                'tagFilter' => $stripTagsFilter,
                'string' => $string,
            ]
        );
    }

    public function testApplyFilter()
    {
        $attributeCode = 'attributeCode';
        $attributeValue = 'attributeValue';
        $attributeLabel = 'attributeLabel';

        $this->attribute->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));

        $this->target->setAttributeModel($this->attribute);

        $this->request->expects($this->once())
            ->method('getParam')
            ->with($attributeCode)
            ->will($this->returnValue($attributeValue));

        $this->frontend->expects($this->once())
            ->method('getOption')
            ->with($attributeValue)
            ->will($this->returnValue($attributeLabel));

        $filterItem = $this->createFilterItem(0, $attributeLabel, $attributeValue, 0);

        $filterItem->expects($this->once())
            ->method('setFilter')
            ->with($this->target)
            ->will($this->returnSelf());

        $filterItem->expects($this->once())
            ->method('setLabel')
            ->with($attributeLabel)
            ->will($this->returnSelf());

        $filterItem->expects($this->once())
            ->method('setValue')
            ->with($attributeValue)
            ->will($this->returnSelf());

        $filterItem->expects($this->once())
            ->method('setCount')
            ->with(0)
            ->will($this->returnSelf());

        $this->state->expects($this->once())
            ->method('addFilter')
            ->with($filterItem)
            ->will($this->returnSelf());

        $result = $this->target->apply($this->request);

        $this->assertEquals($this->target, $result);
    }

    public function testGetItems()
    {
        $attributeCode = 'attributeCode';
        $attributeValue = 'attributeValue';
        $attributeLabel = 'attributeLabel';

        $this->attribute->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));

        $this->target->setAttributeModel($this->attribute);

        $this->request->expects($this->once())
            ->method('getParam')
            ->with($attributeCode)
            ->will($this->returnValue($attributeValue));

        $this->frontend->expects($this->once())
            ->method('getOption')
            ->with($attributeValue)
            ->will($this->returnValue($attributeLabel));

        $filterItem = $this->createFilterItem(0, $attributeLabel, $attributeValue, 0);

        $this->state->expects($this->once())
            ->method('addFilter')
            ->with($filterItem)
            ->will($this->returnSelf());

        $expectedFilterItems = [];

        $result = $this->target->apply($this->request)->getItems();

        $this->assertEquals($expectedFilterItems, $result);
    }

    /**
     * @param int $index
     * @param string $label
     * @param string $value
     * @param int $count
     * @return \Magento\Catalog\Model\Layer\Filter\Item|MockObject
     */
    private function createFilterItem($index, $label, $value, $count)
    {
        $filterItem = $this->getMockBuilder('\Magento\Catalog\Model\Layer\Filter\Item')
            ->disableOriginalConstructor()
            ->setMethods(['setFilter', 'setLabel', 'setValue', 'setCount'])
            ->getMock();

        $filterItem->expects($this->once())
            ->method('setFilter')
            ->with($this->target)
            ->will($this->returnSelf());

        $filterItem->expects($this->once())
            ->method('setLabel')
            ->with($label)
            ->will($this->returnSelf());

        $filterItem->expects($this->once())
            ->method('setValue')
            ->with($value)
            ->will($this->returnSelf());

        $filterItem->expects($this->once())
            ->method('setCount')
            ->with($count)
            ->will($this->returnSelf());

        $this->filterItemFactory->expects($this->at($index))
            ->method('create')
            ->will($this->returnValue($filterItem));

        return $filterItem;
    }
}
