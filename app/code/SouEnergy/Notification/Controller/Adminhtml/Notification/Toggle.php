<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Adminhtml\Notification;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;

class Toggle extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Notification::notifications';

    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        Context $context,
        NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($context);
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id');

        if ($id) {
            try {
                $notification = $this->notificationRepository->getById($id);
                $newStatus = !$notification->getIsActive();
                $notification->setIsActive($newStatus);
                $this->notificationRepository->save($notification);

                $statusLabel = $newStatus ? __('Ativada') : __('Desativada');
                $this->messageManager->addSuccessMessage(
                    __('Notificação #%1 foi %2 com sucesso!', $id, $statusLabel)
                );
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Erro ao alterar status da notificação: %1', $e->getMessage()));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
