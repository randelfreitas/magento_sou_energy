<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Block\Customer;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SouEnergy\Notification\Model\TermsConfig;

class TermsModal extends Template
{
    private CustomerSession $customerSession;
    private CustomerRepositoryInterface $customerRepository;
    private TermsConfig $termsConfig;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        TermsConfig $termsConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->termsConfig = $termsConfig;
    }

    public function shouldShowModal(): bool
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }

        // Não exibir na própria tela de leitura/aceite dos termos
        $fullAction = $this->getRequest()->getFullActionName();
        if ($fullAction === 'souenergy_terms_index') {
            return false;
        }

        $currentSystemVersion = $this->getCurrentVersion();
        $userTermsVersion = $this->getUserVersion();

        return $userTermsVersion !== $currentSystemVersion;
    }

    public function getCurrentVersion(): string
    {
        return $this->termsConfig->getCurrentVersion();
    }

    public function getUserVersion(): ?string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        try {
            $customerId = (int)$this->customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            $termsAttr = $customer->getCustomAttribute('terms_version');
            return $termsAttr ? (string)$termsAttr->getValue() : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getTermsUrl(): string
    {
        return $this->getUrl('souenergy/terms/index');
    }
}
