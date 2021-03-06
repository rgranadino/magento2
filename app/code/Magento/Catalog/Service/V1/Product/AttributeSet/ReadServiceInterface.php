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
namespace Magento\Catalog\Service\V1\Product\AttributeSet;

interface ReadServiceInterface
{
    /**
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet[]
     * @deprecated
     * @see \Magento\Eav\Api\AttributeSetRepositoryInterface::getList
     */
    public function getList();

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet
     * @deprecated
     * @see \Magento\Eav\Api\AttributeSetRepositoryInterface::get
     */
    public function getInfo($attributeSetId);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Service\V1\Data\Eav\Attribute[]
     * @deprecated
     * @see \Magento\Eav\Api\AttributeManagementInterface::getAttributes
     */
    public function getAttributeList($attributeSetId);
}
