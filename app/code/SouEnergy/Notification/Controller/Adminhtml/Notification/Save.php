<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Adminhtml\Notification;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SouEnergy\Notification\Model\NotificationFactory;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Notification::notifications';

    private NotificationFactory $notificationFactory;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        Context $context,
        NotificationFactory $notificationFactory,
        NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($context);
        $this->notificationFactory = $notificationFactory;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $notification = $this->notificationFactory->create();
                
                $customerId = !empty($data['customer_id']) ? (int)$data['customer_id'] : null;
                $actionUrl = !empty($data['action_url']) ? trim((string)$data['action_url']) : null;
                $actionLabel = !empty($data['action_label']) ? trim((string)$data['action_label']) : null;
                $isActive = isset($data['is_active']) ? (bool)$data['is_active'] : true;

                $notification->setCustomerId($customerId);
                $notification->setTitle((string)($data['title'] ?? ''));
                $notification->setMessage((string)($data['message'] ?? ''));
                $notification->setActionUrl($actionUrl);
                $notification->setActionLabel($actionLabel);
                $notification->setIsActive($isActive);
                $notification->setIsRead(false);

                $this->notificationRepository->save($notification);

                $this->messageManager->addSuccessMessage(__('Notificação cadastrada com sucesso!'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Erro ao salvar notificação: %1', $e->getMessage()));
                return $resultRedirect->setPath('*/*/newaction');
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
