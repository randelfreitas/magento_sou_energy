<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Block\Adminhtml\Terms;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use SouEnergy\Notification\Model\TermsConfig;

class Manage extends Template
{
    private TermsConfig $termsConfig;
    private CustomerCollectionFactory $customerCollectionFactory;

    public function __construct(
        Context $context,
        TermsConfig $termsConfig,
        CustomerCollectionFactory $customerCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->termsConfig = $termsConfig;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    public function getCurrentVersion(): string
    {
        return $this->termsConfig->getCurrentVersion();
    }

    public function getCurrentContent(): string
    {
        return $this->termsConfig->getCurrentContent();
    }

    public function getSaveUrl(): string
    {
        return $this->getUrl('*/*/save');
    }

    public function getTotalCustomersCount(): int
    {
        return $this->customerCollectionFactory->create()->getSize();
    }

    public function getUpdatedCustomersCount(): int
    {
        $currentVersion = $this->getCurrentVersion();
        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToFilter('terms_version', ['eq' => $currentVersion]);
        return $collection->getSize();
    }
}
