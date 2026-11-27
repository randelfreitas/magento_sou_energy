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

class View extends Template
{
    private CustomerSession $customerSession;
    private CustomerRepositoryInterface $customerRepository;
    private PolicyConfig $policyConfig;
    private ?string $userVersion = null;
    private ?string $userAcceptedAt = null;
    private bool $customerLoaded = false;

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

    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getCurrentVersion(): string
    {
        return $this->policyConfig->getCurrentVersion();
    }

    public function getCurrentContent(): string
    {
        return $this->policyConfig->getCurrentContent();
    }

    public function getUserVersion(): ?string
    {
        $this->loadCustomerAttributes();
        return $this->userVersion;
    }

    public function getUserAcceptedAt(): ?string
    {
        $this->loadCustomerAttributes();
        return $this->userAcceptedAt;
    }

    public function isUserUpdated(): bool
    {
        return $this->isLoggedIn() && $this->getUserVersion() === $this->getCurrentVersion();
    }

    public function getAcceptUrl(): string
    {
        return $this->getUrl('privacidade/index/accept');
    }

    public function getNotificationsUrl(): string
    {
        return $this->getUrl('notificacoes/index/index');
    }

    public function getLoginUrl(): string
    {
        return $this->getUrl('customer/account/login');
    }

    private function loadCustomerAttributes(): void
    {
        if ($this->customerLoaded || !$this->isLoggedIn()) {
            return;
        }

        $this->customerLoaded = true;

        try {
            $customer = $this->customerRepository->getById((int)$this->customerSession->getCustomerId());
            $versionAttr = $customer->getCustomAttribute(AddCustomerPrivacyAttributes::ATTR_VERSION);
            $acceptedAtAttr = $customer->getCustomAttribute(AddCustomerPrivacyAttributes::ATTR_ACCEPTED_AT);

            $this->userVersion = $versionAttr ? (string)$versionAttr->getValue() : null;
            $this->userAcceptedAt = $acceptedAtAttr ? (string)$acceptedAtAttr->getValue() : null;
        } catch (Exception $e) {
            $this->userVersion = null;
            $this->userAcceptedAt = null;
        }
    }
}
