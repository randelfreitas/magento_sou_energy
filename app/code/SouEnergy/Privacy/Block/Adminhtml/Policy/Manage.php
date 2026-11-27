<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Block\Adminhtml\Policy;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use SouEnergy\Privacy\Model\PolicyConfig;
use SouEnergy\Privacy\Setup\Patch\Data\AddCustomerPrivacyAttributes;

class Manage extends Template
{
    private PolicyConfig $policyConfig;
    private CustomerCollectionFactory $customerCollectionFactory;

    public function __construct(
        Context $context,
        PolicyConfig $policyConfig,
        CustomerCollectionFactory $customerCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->policyConfig = $policyConfig;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    public function getCurrentVersion(): string
    {
        return $this->policyConfig->getCurrentVersion();
    }

    public function getCurrentContent(): string
    {
        return $this->policyConfig->getCurrentContent();
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
        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToFilter(
            AddCustomerPrivacyAttributes::ATTR_VERSION,
            ['eq' => $this->getCurrentVersion()]
        );

        return $collection->getSize();
    }
}
