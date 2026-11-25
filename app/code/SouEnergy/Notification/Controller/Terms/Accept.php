<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Terms;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;

class Accept extends Action
{
    private CustomerSession $customerSession;
    private NotificationCollectionFactory $collectionFactory;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        NotificationCollectionFactory $collectionFactory,
        NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Faça login para aceitar os termos de uso.'));
            return $resultRedirect->setPath('customer/account/login');
        }

        $customerId = (int)$this->customerSession->getCustomerId();

        try {
            // Find any pending notifications about terms and mark them as read
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(
                [NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID],
                [
                    ['eq' => $customerId],
                    ['null' => true]
                ]
            );
            $collection->addFieldToFilter(NotificationInterface::IS_READ, 0);

            foreach ($collection as $notification) {
                if (stripos($notification->getTitle(), 'termo') !== false || stripos($notification->getMessage(), 'termo') !== false) {
                    $notification->setIsRead(true);
                    $this->notificationRepository->save($notification);
                }
            }

            $this->messageManager->addSuccessMessage(__('Você leu e aceitou os Termos de Uso com sucesso!'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Ocorreu um erro ao registrar o aceite dos termos.'));
        }

        return $resultRedirect->setPath('souenergy/notification/index');
    }
}
