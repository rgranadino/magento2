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

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Set default attribute value.
 */
class SetDefaultAttributeValueStep implements TestStepInterface
{
    /**
     * CatalogProductAttribute fixture.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * FixtureFactory object.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product index page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * @constructor
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductEdit $catalogProductEdit
     * @param FixtureFactory $fixtureFactory
     */
    public function __construct(
        CatalogProductAttribute $attribute,
        CatalogProductEdit $catalogProductEdit,
        FixtureFactory $fixtureFactory
    ) {
        $this->attribute = $attribute;
        $this->catalogProductEdit = $catalogProductEdit;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Set Default Attribute Value.
     *
     * @return void
     */
    public function run()
    {
        $product = $this->fixtureFactory->createByCode(
            'catalogProductSimple',
            ['data' => ['custom_attribute' => $this->attribute]]
        );
        $this->catalogProductEdit->getProductForm()->fill($product);
    }
}
