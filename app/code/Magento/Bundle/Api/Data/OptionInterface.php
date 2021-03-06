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

namespace Magento\Bundle\Api\Data;

interface OptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Get option title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get is required option
     *
     * @return bool|null
     */
    public function getRequired();

    /**
     * Get input type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get option position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Get product sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Get product links
     *
     * @return \Magento\Bundle\Api\Data\LinkInterface[]|null
     */
    public function getProductLinks();
}
