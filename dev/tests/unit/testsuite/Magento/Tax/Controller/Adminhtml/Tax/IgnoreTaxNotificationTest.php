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
namespace Magento\Tax\Controller\Adminhtml\Tax;

use Magento\TestFramework\Helper\ObjectManager;

class IgnoreTaxNotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $objectManager = new ObjectManager($this);
        $cacheTypeList = $this->getMockBuilder('\Magento\Framework\App\Cache\TypeList')
            ->disableOriginalConstructor()
            ->setMethods(['cleanType'])
            ->getMock();
        $cacheTypeList->expects($this->once())
            ->method('cleanType')
            ->with('block_html')
            ->will($this->returnValue(null));

        $request = $this->getMockBuilder('\Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getParam'])
            ->getMock();
        $request->expects($this->once())
            ->method('getParam')
            ->will($this->returnValue('tax'));

        $response = $this->getMockBuilder('\Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods(['setRedirect'])
            ->getMock();

        $config = $this->getMockBuilder('\Magento\Core\Model\Resource\Config')
            ->disableOriginalConstructor()
            ->setMethods(['saveConfig'])
            ->getMock();
        $config->expects($this->once())
            ->method('saveConfig')
            ->with('tax/notification/ignore_tax', 1, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, 0)
            ->will($this->returnValue(null));

        $manager = $this->getMockBuilder('\Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['get', 'create', 'configure'])
            ->getMock();
        $manager->expects($this->any())
            ->method('get')
            ->will($this->returnValue($config));

        $notification = $objectManager->getObject(
            'Magento\Tax\Controller\Adminhtml\Tax\IgnoreTaxNotification',
            [
                'objectManager' => $manager,
                'cacheTypeList' => $cacheTypeList,
                'request' => $request,
                'response' => $response
            ]
        );

        // No exception thrown
        $notification->execute();
    }
}
