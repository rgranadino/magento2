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
namespace Magento\Ui\Component;

use Magento\Ui\DataProvider\Manager;
use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Ui\Component\Filter\FilterAbstract;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Ui\Component\Filter\FilterPool as FilterPoolProvider;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class FilterPool
 */
class FilterPool extends AbstractView
{
    /**
     * Filters pool
     *
     * @var FilterPoolProvider
     */
    protected $filterPoolProvider;

    /**
     * Constructor
     *
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param DataProviderFactory $dataProviderFactory
     * @param Manager $dataProviderManager
     * @param FilterPoolProvider $filterPoolProvider
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        DataProviderFactory $dataProviderFactory,
        Manager $dataProviderManager,
        FilterPoolProvider $filterPoolProvider,
        array $data = []
    ) {
        $this->filterPoolProvider = $filterPoolProvider;
        parent::__construct(
            $context,
            $renderContext,
            $contentTypeFactory,
            $configFactory,
            $configBuilder,
            $dataProviderFactory,
            $dataProviderManager,
            $data
        );
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        $configData = $this->getDefaultConfiguration();
        if ($this->hasData('config')) {
            $configData = array_merge($configData, $this->getData('config'));
        }
        $this->prepareConfiguration($configData);
        $this->updateDataCollection();
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        $meta = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $fields = [];
        if (isset($meta['fields'])) {
            foreach ($meta['fields'] as $name => $config) {
                if (isset($config['filterable']) && $config['filterable'] === false) {
                    continue;
                }
                $fields[$name] = $config;
            }
        }
        return $fields;
    }

    /**
     * Get active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $metaData = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $metaData = $metaData['fields'];
        $filters = [];
        $filterData = $this->prepareFilterString(
            $this->renderContext->getRequestParam(FilterAbstract::FILTER_VAR)
        );
        foreach ($filterData as $field => $value) {
            if (isset($metaData[$field]['filter_type'])) {
                $filters[$field] = [
                    'label' => $metaData[$field]['label'],
                    'current_display_value' => $value
                ];
            }
        }

        return $filters;
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $collection = $this->renderContext->getStorage()->getDataCollection($this->getParentName());

        $metaData = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $metaData = $metaData['fields'];
        $filterData = $this->prepareFilterString(
            $this->renderContext->getRequestParam(FilterAbstract::FILTER_VAR)
        );
        foreach ($filterData as $field => $value) {
            if (!isset($metaData[$field]['filter_type'])) {
                continue;
            }
            $condition = $this->filterPoolProvider->getFilter($metaData[$field]['filter_type'])->getCondition($value);
            if ($condition !== null) {
                $collection->addFilter($field, $field, $condition);
            }
        }
    }

    /**
     * Get list of required filters
     *
     * @return array
     */
    protected function getListOfRequiredFilters()
    {
        $result = [];
        foreach ($this->getFields() as $field) {
            $result[] = isset($field['filter_type']) ? $field['filter_type'] : $field['input_type'];
        }

        return $result;
    }

    /**
     * Decode filter string
     *
     * @param string $filterString
     * @return array
     */
    protected function prepareFilterString($filterString)
    {
        $data = [];
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive(
            $data,
            // @codingStandardsIgnoreStart
            /**
             * Decodes URL-encoded string and trims whitespaces from the beginning and end of a string
             *
             * @param string $value
             */
            // @codingStandardsIgnoreEnd
            function (&$value) {
                $value = trim(rawurldecode($value));
            }
        );
        return $data;
    }
}
