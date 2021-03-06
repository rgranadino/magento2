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
namespace Magento\Sales\Model\Service;

/**
 * Class OrderUnHoldTest
 */
class OrderServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Service\OrderService
     */
    protected $orderService;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Api\OrderStatusHistoryRepositoryInterface
     */
    protected $orderStatusHistoryRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilderMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteriaMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilderMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\Filter
     */
    protected $filterMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\OrderNotifier
     */
    protected $orderNotifierMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Order
     */
    protected $orderMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Order\Status\History
     */
    protected $orderStatusHistoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Api\Data\OrderStatusHistorySearchResultInterface
     */
    protected $orderSearchResultMock;

    protected function setUp()
    {
        $this->orderRepositoryMock = $this->getMockBuilder(
            'Magento\Sales\Api\OrderRepositoryInterface'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderStatusHistoryRepositoryMock= $this->getMockBuilder(
            'Magento\Sales\Api\OrderStatusHistoryRepositoryInterface'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(
            'Magento\Framework\Api\SearchCriteriaBuilder'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaMock = $this->getMockBuilder(
            'Magento\Framework\Api\SearchCriteria'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterBuilderMock = $this->getMockBuilder(
            'Magento\Framework\Api\FilterBuilder'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterMock = $this->getMockBuilder(
            'Magento\Framework\Api\Filter'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderNotifierMock = $this->getMockBuilder(
            'Magento\Sales\Model\OrderNotifier'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderMock = $this->getMockBuilder(
            'Magento\Sales\Model\Order'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderStatusHistoryMock = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Status\History'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderSearchResultMock = $this->getMockBuilder(
            'Magento\Sales\Api\Data\OrderStatusHistorySearchResultInterface'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderService = new \Magento\Sales\Model\Service\OrderService(
            $this->orderRepositoryMock,
            $this->orderStatusHistoryRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filterBuilderMock,
            $this->orderNotifierMock
        );
    }

    /**
     * test for Order::cancel()
     */
    public function testCancel()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())
            ->method('cancel')
            ->willReturn($this->orderMock);
        $this->assertTrue($this->orderService->cancel(123));
    }

    public function testGetCommentsList()
    {
        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with('parent_id')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with(123)
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->filterMock);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(['eq' => $this->filterMock])
            ->willReturn($this->filterBuilderMock);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->orderStatusHistoryRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->orderSearchResultMock);
        $this->assertEquals($this->orderSearchResultMock, $this->orderService->getCommentsList(123));
    }

    public function testAddComment()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())
            ->method('addStatusHistory')
            ->with($this->orderStatusHistoryMock)
            ->willReturn($this->orderMock);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderMock)
            ->willReturn([]);
        $this->assertTrue($this->orderService->addComment(123, $this->orderStatusHistoryMock));
    }

    public function testNotify()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn($this->orderMock);
        $this->orderNotifierMock->expects($this->once())
            ->method('notify')
            ->with($this->orderMock)
            ->willReturn(true);
        $this->assertTrue($this->orderService->notify(123));
    }

    public function testGetStatus()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())
            ->method('getStatus')
            ->willReturn('test-status');
        $this->assertEquals('test-status', $this->orderService->getStatus(123));
    }

    public function testHold()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())
            ->method('hold')
            ->willReturn($this->orderMock);
        $this->assertTrue($this->orderService->hold(123));
    }

    public function testUnHold()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())
            ->method('unHold')
            ->willReturn($this->orderMock);
        $this->assertTrue($this->orderService->unHold(123));
    }
}
