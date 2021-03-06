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
namespace Magento\Catalog\Model\Product\Attribute;

use \Magento\Framework\Exception\StateException;

class SetManagement implements \Magento\Catalog\Api\AttributeSetManagementInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeSetManagementInterface
     */
    protected $attributeSetManagement;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Magento\Eav\Api\AttributeSetManagementInterface $attributeSetManagement
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetManagementInterface $attributeSetManagement,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet, $skeletonId)
    {
        $this->validateSkeletonSet($skeletonId);
        return $this->attributeSetManagement->create(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSet,
            $skeletonId
        );
    }

    /**
     * @param int $skeletonId
     * @return void
     * @throws StateException
     */
    protected function validateSkeletonSet($skeletonId)
    {
        try {
            $skeletonSet = $this->attributeSetRepository->get($skeletonId);
            $productEntityId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
            if ($skeletonSet->getEntityTypeId() != $productEntityId) {
                throw new StateException('Can not create attribute set based on non product attribute set.');
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            throw new StateException('Can not create attribute set based on not existing attribute set');
        }
    }
}
