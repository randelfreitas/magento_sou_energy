<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Controller\Adminhtml\Policy;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;
use SouEnergy\Notification\Model\NotificationFactory;
use SouEnergy\Privacy\Model\PolicyConfig;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Privacy::manage';

    private PolicyConfig $policyConfig;
    private NotificationFactory $notificationFactory;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        Context $context,
        PolicyConfig $policyConfig,
        NotificationFactory $notificationFactory,
        NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($context);
        $this->policyConfig = $policyConfig;
        $this->notificationFactory = $notificationFactory;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $newVersion = trim((string)$this->getRequest()->getPostValue('version', ''));
        $content = trim((string)$this->getRequest()->getPostValue('content', ''));
        $title = trim((string)$this->getRequest()->getPostValue('title', ''));
        $message = trim((string)$this->getRequest()->getPostValue('message', ''));

        if (empty($newVersion)) {
            $this->messageManager->addErrorMessage(__('Por favor, informe a nova versão da Política de Privacidade.'));
            return $resultRedirect->setPath('*/*/index');
        }

        if (empty($content)) {
            $this->messageManager->addErrorMessage(__('Por favor, informe o texto da Política de Privacidade.'));
            return $resultRedirect->setPath('*/*/index');
        }

        try {
            $this->policyConfig->setCurrentVersion($newVersion);
            $this->policyConfig->setCurrentContent($content);

            $notificationTitle = !empty($title) ? $title : sprintf('Atualização da Política de Privacidade (%s)', $newVersion);
            $notificationMessage = !empty($message) ? $message : sprintf(
                'Publicamos uma nova versão da Política de Privacidade (%s). Por favor, acesse o link para ler e confirmar seu aceite.',
                $newVersion
            );

            $notification = $this->notificationFactory->create();
            $notification->setCustomerId(null);
            $notification->setTitle($notificationTitle);
            $notification->setMessage($notificationMessage);
            $notification->setActionUrl(PolicyConfig::POLICY_FRONTEND_ROUTE);
            $notification->setActionLabel('Acessar Política de Privacidade');
            $notification->setIsActive(true);
            $notification->setIsRead(false);

            $this->notificationRepository->save($notification);

            $this->messageManager->addSuccessMessage(
                __('Versão vigente da Política de Privacidade atualizada para "%1" com sucesso! Notificação enviada para todos os usuários.', $newVersion)
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Erro ao atualizar a Política de Privacidade: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
