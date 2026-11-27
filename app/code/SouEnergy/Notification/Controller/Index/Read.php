<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Index;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;

class Read extends Action
{
    private CustomerSession $customerSession;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $resultRedirect->setPath('customer/account/login');
        }

        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $customerId = (int)$this->customerSession->getCustomerId();
                $notification = $this->notificationRepository->getById($id);

                if ($notification->getCustomerId() === null || $notification->getCustomerId() === $customerId) {
                    $notification->setIsRead(true);
                    $this->notificationRepository->save($notification);
                    $this->messageManager->addSuccessMessage(__('Notificação marcada como lida!'));
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Não foi possível atualizar a notificação.'));
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
