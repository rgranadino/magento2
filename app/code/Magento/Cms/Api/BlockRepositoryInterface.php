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
namespace Magento\Cms\Api;

/**
 * Interface BlockRepositoryInterface
 */
interface BlockRepositoryInterface
{
    /**
     * Save Block data
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return \Magento\Cms\Api\Data\BlockInterface
     */
    public function save(\Magento\Cms\Api\Data\BlockInterface $block);

    /**
     * Load Block data by given Block Identity
     *
     * @param string $blockId
     * @return \Magento\Cms\Api\Data\BlockInterface
     */
    public function get($blockId);

    /**
     * Load Block data collection by given search criteria
     *
     * @param \Magento\Cms\Api\BlockCriteriaInterface $criteria
     * @return \Magento\Cms\Api\Data\BlockCollectionInterface
     */
    public function getList(\Magento\Cms\Api\BlockCriteriaInterface $criteria);

    /**
     * Delete Block
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return bool
     */
    public function delete(\Magento\Cms\Api\Data\BlockInterface $block);

    /**
     * Delete Block by given Block Identity
     *
     * @param string $blockId
     * @return bool
     */
    public function deleteById($blockId);
}
