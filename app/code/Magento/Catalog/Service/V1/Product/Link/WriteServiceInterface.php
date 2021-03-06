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
 
namespace Magento\Catalog\Service\V1\Product\Link;

/**
 * @todo remove this interface
 * @deprecated
 */
interface WriteServiceInterface
{
    /**
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink[] $assignedProducts
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::assign
     */
    public function assign($productSku, array $assignedProducts, $type);

    /**
     * Update product link
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink $linkedProduct
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::update
     */
    public function update($productSku, Data\ProductLink $linkedProduct, $type);

    /**
     * Remove the product link from a specific product
     *
     * @param string $productSku
     * @param string $linkedProductSku
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::remove
     */
    public function remove($productSku, $linkedProductSku, $type);
}
