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
namespace Magento\ProductAlert\Controller\Unsubscribe;

use Magento\Framework\Exception\NoSuchEntityException;

class Stock extends \Magento\ProductAlert\Controller\Unsubscribe
{
    /** @var  \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->productRepository = $productRepository;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }

        try {
            $product = $this->productRepository->getById($productId);
            if (!$product->isVisibleInCatalog()) {
                throw new NoSuchEntityException();
            }

            $model = $this->_objectManager->create(
                'Magento\ProductAlert\Model\Stock'
            )->setCustomerId(
                $this->_customerSession->getCustomerId()
            )->setProductId(
                $product->getId()
            )->setWebsiteId(
                $this->_objectManager->get('Magento\Framework\StoreManagerInterface')->getStore()->getWebsiteId()
            )->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $this->messageManager->addSuccess(__('You will no longer receive stock alerts for this product.'));
        } catch (NoSuchEntityException $noEntityException) {
            $this->messageManager->addError(__('The product was not found.'));
            $this->_redirect('customer/account/');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($product->getProductUrl());
    }
}
