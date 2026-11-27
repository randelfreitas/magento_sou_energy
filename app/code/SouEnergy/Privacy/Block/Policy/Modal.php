<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Block\Policy;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SouEnergy\Privacy\Model\PolicyConfig;
use SouEnergy\Privacy\Setup\Patch\Data\AddCustomerPrivacyAttributes;

class Modal extends Template
{
    private CustomerSession $customerSession;
    private CustomerRepositoryInterface $customerRepository;
    private PolicyConfig $policyConfig;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        PolicyConfig $policyConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->policyConfig = $policyConfig;
    }

    public function shouldShowModal(): bool
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }

        if ($this->getRequest()->getFullActionName() === 'souenergy_privacy_index_index') {
            return false;
        }

        return $this->getUserVersion() !== $this->getCurrentVersion();
    }

    public function getCurrentVersion(): string
    {
        return $this->policyConfig->getCurrentVersion();
    }

    public function getUserVersion(): ?string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        try {
            $customer = $this->customerRepository->getById((int)$this->customerSession->getCustomerId());
            $attr = $customer->getCustomAttribute(AddCustomerPrivacyAttributes::ATTR_VERSION);
            return $attr ? (string)$attr->getValue() : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getPolicyUrl(): string
    {
        return $this->getUrl(PolicyConfig::POLICY_FRONTEND_ROUTE);
    }
}
