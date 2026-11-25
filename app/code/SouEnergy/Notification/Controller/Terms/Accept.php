<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Terms;

use DateTime;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;
use SouEnergy\Notification\Model\TermsConfig;

class Accept extends Action
{
    private CustomerSession $customerSession;
    private NotificationCollectionFactory $collectionFactory;
    private NotificationRepositoryInterface $notificationRepository;
    private CustomerRepositoryInterface $customerRepository;
    private TermsConfig $termsConfig;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        NotificationCollectionFactory $collectionFactory,
        NotificationRepositoryInterface $notificationRepository,
        CustomerRepositoryInterface $customerRepository,
        TermsConfig $termsConfig
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->notificationRepository = $notificationRepository;
        $this->customerRepository = $customerRepository;
        $this->termsConfig = $termsConfig;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Faça login para aceitar os termos de uso.'));
            return $resultRedirect->setPath('customer/account/login');
        }

        $customerId = (int)$this->customerSession->getCustomerId();
        $currentVersion = $this->termsConfig->getCurrentVersion();

        try {
            // 1. Grava a versão vigente atual no perfil do cliente
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute('terms_version', $currentVersion);
            $customer->setCustomAttribute('terms_accepted_at', (new DateTime())->format('Y-m-d H:i:s'));
            $this->customerRepository->save($customer);

            // 2. Marca notificações pendentes de termos como lidas
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(
                [NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID],
                [
                    ['eq' => $customerId],
                    ['null' => true],
                    ['eq' => 0]
                ]
            );
            $collection->addFieldToFilter(NotificationInterface::IS_READ, 0);

            foreach ($collection as $notification) {
                $url = (string)$notification->getActionUrl();
                if (stripos($url, 'terms') !== false || stripos($notification->getTitle(), 'termo') !== false) {
                    $notification->setIsRead(true);
                    $this->notificationRepository->save($notification);
                }
            }

            $this->messageManager->addSuccessMessage(
                __('Você leu e aceitou os novos Termos de Uso (%1) com sucesso!', $currentVersion)
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Ocorreu um erro ao registrar o aceite dos termos: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('souenergy/notification/index');
    }
}
