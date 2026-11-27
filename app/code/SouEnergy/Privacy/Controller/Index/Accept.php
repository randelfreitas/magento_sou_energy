<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Controller\Index;

use DateTime;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;
use SouEnergy\Privacy\Model\PolicyConfig;
use SouEnergy\Privacy\Setup\Patch\Data\AddCustomerPrivacyAttributes;

class Accept extends Action
{
    private CustomerSession $customerSession;
    private NotificationCollectionFactory $collectionFactory;
    private NotificationRepositoryInterface $notificationRepository;
    private CustomerRepositoryInterface $customerRepository;
    private PolicyConfig $policyConfig;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        NotificationCollectionFactory $collectionFactory,
        NotificationRepositoryInterface $notificationRepository,
        CustomerRepositoryInterface $customerRepository,
        PolicyConfig $policyConfig
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->notificationRepository = $notificationRepository;
        $this->customerRepository = $customerRepository;
        $this->policyConfig = $policyConfig;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Faça login para aceitar a Política de Privacidade.'));
            return $resultRedirect->setPath('customer/account/login');
        }

        $customerId = (int)$this->customerSession->getCustomerId();
        $currentVersion = $this->policyConfig->getCurrentVersion();

        try {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute(AddCustomerPrivacyAttributes::ATTR_VERSION, $currentVersion);
            $customer->setCustomAttribute(
                AddCustomerPrivacyAttributes::ATTR_ACCEPTED_AT,
                (new DateTime())->format('Y-m-d H:i:s')
            );
            $this->customerRepository->save($customer);

            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(
                [NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID],
                [
                    ['eq' => $customerId],
                    ['null' => true]
                ]
            );
            $collection->addFieldToFilter(NotificationInterface::IS_READ, 0);
            $collection->addFieldToFilter(NotificationInterface::ACTION_URL, PolicyConfig::POLICY_FRONTEND_ROUTE);

            foreach ($collection as $notification) {
                $notification->setIsRead(true);
                $this->notificationRepository->save($notification);
            }

            $this->messageManager->addSuccessMessage(
                __('Você leu e aceitou a nova Política de Privacidade (%1) com sucesso!', $currentVersion)
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Ocorreu um erro ao registrar o aceite da Política de Privacidade: %1', $e->getMessage())
            );
        }

        return $resultRedirect->setPath('customer/account');
    }
}
