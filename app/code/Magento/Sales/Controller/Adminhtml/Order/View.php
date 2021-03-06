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
namespace Magento\Sales\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;

class View extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * View order detail
     *
     * @return void
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $this->_initAction();
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Orders'));
            } catch (\Magento\Framework\App\Action\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('sales/order/index');
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('Exception occurred during order load'));
                $this->_redirect('sales/order/index');
                return;
            }
            $this->_view->getPage()->getConfig()->getTitle()->prepend(sprintf("#%s", $order->getRealOrderId()));
            $this->_view->renderLayout();
        }
    }
}
