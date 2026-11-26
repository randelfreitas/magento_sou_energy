<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Block\Customer;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SouEnergy\Notification\Model\TermsConfig;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

class TermsModal extends Template
{
    private CustomerSession $customerSession;
    private CustomerRepositoryInterface $customerRepository;
    private TermsConfig $termsConfig;
    private NotificationCollectionFactory $notificationCollectionFactory;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        TermsConfig $termsConfig,
        NotificationCollectionFactory $notificationCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->termsConfig = $termsConfig;
        $this->notificationCollectionFactory = $notificationCollectionFactory;
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

        // Se o usuário já aceitou a versão vigente, não exibe
        if ($userTermsVersion === $currentSystemVersion) {
            return false;
        }

        // Verifica se a notificação dos termos está ativa
        // Se a notificação foi desativada pelo Admin, o modal NÃO deve aparecer
        if (!$this->isTermsNotificationActive()) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se a notificação de termos mais recente está ativa
     */
    public function isTermsNotificationActive(): bool
    {
        try {
            $collection = $this->notificationCollectionFactory->create();
            $collection->addFieldToFilter(
                ['action_url', 'title'],
                [
                    ['like' => '%terms%'],
                    ['like' => '%termo%']
                ]
            );
            $collection->setOrder('entity_id', 'DESC');
            $latestTermsNotification = $collection->getFirstItem();

            if ($latestTermsNotification && $latestTermsNotification->getId()) {
                return (bool)$latestTermsNotification->getIsActive();
            }

            return true;
        } catch (Exception $e) {
            return true;
        }
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
