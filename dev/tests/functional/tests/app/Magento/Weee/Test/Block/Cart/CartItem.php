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

namespace Magento\Weee\Test\Block\Cart;

use Mtf\Client\Element\Locator;

/**
 * Product item fpt block on cart page
 */
class CartItem extends \Magento\Checkout\Test\Block\Cart\CartItem
{
    /**
     * Fpt price block selector
     *
     * @var string
     */

    protected $priceFptBlock = './/td[@class="col price"]';

    /**
     * Fpt subtotal block selector
     *
     * @var string
     */
    protected $subtotalFptBlock = './/td[@class="col subtotal"]';

    /**
     * Get block price fpt
     *
     * @return \Magento\Weee\Test\Block\Cart\CartItem\Fpt
     */
    public function getPriceFptBlock()
    {
        return $this->blockFactory->create(
            'Magento\Weee\Test\Block\Cart\CartItem\Fpt',
            ['element' => $this->_rootElement->find($this->priceFptBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get block subtotal fpt
     *
     * @return \Magento\Weee\Test\Block\Cart\CartItem\Fpt
     */
    public function getSubtotalFptBlock()
    {
        return $this->blockFactory->create(
            'Magento\Weee\Test\Block\Cart\CartItem\Fpt',
            ['element' => $this->_rootElement->find($this->subtotalFptBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
