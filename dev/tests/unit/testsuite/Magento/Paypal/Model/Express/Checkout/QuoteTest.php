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
namespace Magento\Paypal\Model\Express\Checkout;

/**
 * Class QuoteTest
 */
class QuoteTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Express\Checkout\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Customer\Api\Data\AddressDataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressBuilderMock;

    /**
     * @var \Magento\Framework\Object\Copy|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $copyObjectMock;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilderMock;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var \Magento\Customer\Model\Session as CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \Magento\Sales\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMock;

    public function setUp()
    {
        $this->addressBuilderMock = $this->getMock(
            'Magento\Customer\Api\Data\AddressDataBuilder',
            ['populateWithArray', 'create'],
            [],
            '',
            false
        );
        $this->copyObjectMock = $this->getMock(
            'Magento\Framework\Object\Copy',
            [],
            [],
            '',
            false
        );
        $this->customerBuilderMock = $this->getMock(
            'Magento\Customer\Api\Data\CustomerDataBuilder',
            [
                'populateWithArray', 'setEmail', 'setPrefix', 'setFirstname', 'setMiddlename',
                'setLastname', 'setSuffix', 'create'
            ],
            [],
            '',
            false
        );
        $this->customerRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Customer\Api\CustomerRepositoryInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->customerSessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            [],
            [],
            '',
            false
        );
        $this->quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            [],
            [],
            '',
            false
        );
        $this->addressMock = $this->getMock('Magento\Sales\Model\Quote\Address', [], [], '', false);

        $this->quote = new \Magento\Paypal\Model\Express\Checkout\Quote(
            $this->addressBuilderMock,
            $this->customerBuilderMock,
            $this->customerRepositoryMock,
            $this->copyObjectMock,
            $this->customerSessionMock
        );
    }

    public function testPrepareQuoteForNewCustomer()
    {
        $this->quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressMock);
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);
        $customerAddressMock = $this->getMockBuilder('Magento\Customer\Api\Data\AddressInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->addressMock->expects($this->once())
            ->method('exportCustomerAddress')
            ->willReturn($customerAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);
        $this->addressMock->expects($this->exactly(2))
            ->method('getData')
            ->willReturn([]);
        $this->addressBuilderMock->expects($this->exactly(2))
            ->method('populateWithArray')
            ->willReturnSelf();
        $customerDataMock = $this->getMockBuilder('Magento\Customer\Api\Data\CustomerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->willReturnSelf();
        $this->customerBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($customerDataMock);
        $this->assertInstanceOf(
            'Magento\Sales\Model\Quote',
            $this->quote->prepareQuoteForNewCustomer($this->quoteMock)
        );
    }
}