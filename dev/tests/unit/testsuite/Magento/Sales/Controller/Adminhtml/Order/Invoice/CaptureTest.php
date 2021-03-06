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
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Backend\App\Action;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class CaptureTest
 * @package Magento\Sales\Controller\Adminhtml\Order\Invoice
 */
class CaptureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlagMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\Invoice\Capture
     */
    protected $controller;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');

        $this->messageManagerMock = $this->getMockBuilder('Magento\Framework\Message\Manager')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sessionMock = $this->getMockBuilder('Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->actionFlagMock = $this->getMockBuilder('Magento\Framework\App\ActionFlag')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->helperMock = $this->getMockBuilder('Magento\Backend\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $contextMock = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));
        $contextMock->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManagerMock));
        $contextMock->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($this->sessionMock));
        $contextMock->expects($this->any())
            ->method('getActionFlag')
            ->will($this->returnValue($this->actionFlagMock));
        $contextMock->expects($this->any())
            ->method('getHelper')
            ->will($this->returnValue($this->helperMock));

        $this->controller = $objectManager->getObject(
            'Magento\Sales\Controller\Adminhtml\Order\Invoice\Capture',
            ['context' => $contextMock]
        );
    }

    public function testExecute()
    {
        $invoiceId = 2;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('invoice_id')
            ->will($this->returnValue($invoiceId));

        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(['setIsInProcess', '__wakeup'])
            ->getMock();

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceMock->expects($this->once())
            ->method('capture');
        $invoiceMock->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));

        $transactionMock = $this->getMockBuilder('Magento\Framework\DB\Transaction')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $transactionMock->expects($this->at(0))
            ->method('addObject')
            ->with($invoiceMock)
            ->will($this->returnSelf());
        $transactionMock->expects($this->at(1))
            ->method('addObject')
            ->with($orderMock)
            ->will($this->returnSelf());
        $transactionMock->expects($this->at(2))
            ->method('save');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with('The invoice has been captured.');

        $invoiceMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();

        $this->objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('Magento\Sales\Model\Order\Invoice')
            ->willReturn($invoiceMock);

        $this->objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with('Magento\Framework\DB\Transaction')
            ->will($this->returnValue($transactionMock));

        $this->assertNull($this->controller->execute());
    }

    public function testExecuteNoInvoice()
    {
        $invoiceId = 2;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('invoice_id')
            ->will($this->returnValue($invoiceId));

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceMock->expects($this->once())
            ->method('load')
            ->willReturn(null);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Sales\Model\Order\Invoice')
            ->willReturn($invoiceMock);

        $this->assertNull($this->controller->execute());
    }

    public function testExecuteModelException()
    {
        $invoiceId = 2;

        $message = 'model exception';
        $e = new \Magento\Framework\Model\Exception($message);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('invoice_id')
            ->will($this->returnValue($invoiceId));

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceMock->expects($this->once())
            ->method('capture')
            ->will($this->throwException($e));

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with($message);

        $invoiceMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Sales\Model\Order\Invoice')
            ->willReturn($invoiceMock);

        $this->assertNull($this->controller->execute());
    }

    public function testExecuteException()
    {
        $invoiceId = 2;

        $message = 'Invoice capturing error';
        $e = new \Exception($message);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('invoice_id')
            ->will($this->returnValue($invoiceId));

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceMock->expects($this->once())
            ->method('capture')
            ->will($this->throwException($e));

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with($message);

        $invoiceMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Sales\Model\Order\Invoice')
            ->willReturn($invoiceMock);

        $this->assertNull($this->controller->execute());
    }
}
