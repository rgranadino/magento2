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

namespace Magento\Catalog\Model\Layer\Category;

class FilterableAttributeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Search\FilterableAttributeList
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $collectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    protected function setUp()
    {
        $this->collectionFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory', array('create'), array(), '', false);

        $this->storeManagerMock = $this->getMock(
            '\Magento\Framework\StoreManagerInterface', array(), array(), '', false
        );

        $this->layerMock = $this->getMock(
            'Magento\Catalog\Model\Layer\Search', array(), array(), '', false
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Layer\Resolver $layerResolver */
        $layerResolver = $this->getMockBuilder('\Magento\Catalog\Model\Layer\Resolver')
            ->disableOriginalConstructor()
            ->setMethods(['get', 'create'])
            ->getMock();
        $layerResolver->expects($this->any())
            ->method($this->anything())
            ->will($this->returnValue($this->layerMock));

        $this->model = new \Magento\Catalog\Model\Layer\Search\FilterableAttributeList(
            $this->collectionFactoryMock,
            $this->storeManagerMock,
            $layerResolver
        );

    }

    public function testGetListWithEmptyIds()
    {
        $productCollectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '', false
        );
        $this->layerMock->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue($productCollectionMock));
        $productCollectionMock->expects($this->once())->method('getSetIds')->will($this->returnValue(array()));

        $this->collectionFactoryMock->expects($this->never())->method('create');
        $this->assertEquals(array(), $this->model->getList());
    }

    public function testGetList()
    {
        $productCollectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '', false
        );
        $this->layerMock->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue($productCollectionMock));
        $setIds = array(2, 3, 5);
        $productCollectionMock->expects($this->once())->method('getSetIds')->will($this->returnValue($setIds));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $storeId = 4321;
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\Collection', array(), array(), '', false
        );
        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($collectionMock));

        $collectionMock
            ->expects($this->once())
            ->method('setItemObjectClass')
            ->with('Magento\Catalog\Model\Resource\Eav\Attribute')
            ->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('setAttributeSetFilter')
            ->with($setIds)
            ->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('addStoreLabel')
            ->with($storeId)
            ->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('setOrder')
            ->with('position', 'ASC');
        $collectionMock->expects($this->once())->method('addIsFilterableInSearchFilter')->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('load');

        $this->assertEquals($collectionMock, $this->model->getList());
    }
}
