<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Adminhtml\Notification;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Notification::notifications';

    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(Context $context, NotificationRepositoryInterface $notificationRepository)
    {
        parent::__construct($context);
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id');

        if ($id) {
            try {
                $this->notificationRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Notificação excluída com sucesso.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Erro ao excluir notificação: %1', $e->getMessage()));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
