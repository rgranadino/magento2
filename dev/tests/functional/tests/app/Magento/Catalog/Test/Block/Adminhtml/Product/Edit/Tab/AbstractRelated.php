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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class AbstractRelated
 * Base class for related products tab
 */
abstract class AbstractRelated extends Tab
{
    /**
     * Type related products
     *
     * @var string
     */
    protected $relatedType = '';

    /**
     * Select related products
     *
     * @param array $data
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $data, Element $element = null)
    {
        if (isset($data[$this->relatedType]['value'])) {
            $context = $element ? $element : $this->_rootElement;
            $relatedBlock = $this->getRelatedGrid($context);

            foreach ($data[$this->relatedType]['value'] as $product) {
                $relatedBlock->searchAndSelect(['sku' => $product['sku']]);
            }
        }

        return $this;
    }

    /**
     * Get data of tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $relatedBlock = $this->getRelatedGrid($element);
        $columns = [
            'entity_id',
            'name',
            'sku',
        ];
        $relatedProducts = $relatedBlock->getRowsData($columns);

        return [$this->relatedType => $relatedProducts];
    }

    /**
     * Return related products grid
     *
     * @param Element $element
     * @return Grid
     */
    abstract protected function getRelatedGrid(Element $element = null);
}
