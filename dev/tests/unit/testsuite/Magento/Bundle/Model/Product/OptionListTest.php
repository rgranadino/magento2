<?php
/**
 *
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
namespace Magento\Bundle\Model\Product;

class OptionListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Product\OptionList
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkListMock;

    protected function setUp()
    {
        $this->typeMock = $this->getMock('\Magento\Bundle\Model\Product\Type', [], [], '', false);
        $this->optionBuilderMock = $this->getMock(
            '\Magento\Bundle\Api\Data\OptionDataBuilder',
            ['populateWithArray', 'setOptionId', 'setTitle', 'setProductLinks', 'create', 'setSku'],
            [],
            '',
            false
        );
        $this->linkListMock = $this->getMock('\Magento\Bundle\Model\Product\LinksList', [], [], '', false);
        $this->model = new \Magento\Bundle\Model\Product\OptionList(
            $this->typeMock,
            $this->optionBuilderMock,
            $this->linkListMock
        );
    }

    public function testGetItems()
    {
        $optionId = 1;
        $optionData= ['title' => 'test title'];
        $productSku = 'product_sku';

        $productMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $productMock->expects($this->once())->method('getSku')->willReturn($productSku);

        $optionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            ['getOptionId', 'getData', 'getTitle', 'getDefaultTitle'],
            [],
            '',
            false
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $optionsCollMock = $objectManager->getCollectionMock(
            '\Magento\Bundle\Model\Resource\Option\Collection',
            [$optionMock]
        );
        $this->typeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($productMock)
            ->willReturn($optionsCollMock);

        $optionMock->expects($this->exactly(2))->method('getOptionId')->willReturn($optionId);
        $optionMock->expects($this->once())->method('getData')->willReturn($optionData);
        $optionMock->expects($this->once())->method('getTitle')->willReturn(null);
        $optionMock->expects($this->once())->method('getDefaultTitle')->willReturn($optionData['title']);

        $linkMock = $this->getMock('\Magento\Bundle\Api\Data\LinkInterface');
        $this->linkListMock->expects($this->once())
            ->method('getItems')
            ->with($productMock, $optionId)
            ->willReturn([$linkMock]);
        $this->optionBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($optionData)
            ->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())->method('setOptionId')->with($optionId)->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())
            ->method('setTitle')
            ->with($optionData['title'])
            ->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())->method('setSku')->with($productSku)->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())
            ->method('setProductLinks')
            ->with([$linkMock])
            ->willReturnSelf();
        $newOptionMock = $this->getMock('\Magento\Bundle\Api\Data\OptionInterface');
        $this->optionBuilderMock->expects($this->once())->method('create')->willReturn($newOptionMock);

        $this->assertEquals(
            [$newOptionMock],
            $this->model->getItems($productMock)
        );
    }
}
