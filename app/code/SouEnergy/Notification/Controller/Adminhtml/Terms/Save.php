<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Adminhtml\Terms;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;
use SouEnergy\Notification\Model\NotificationFactory;
use SouEnergy\Notification\Model\TermsConfig;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Notification::notifications';

    private TermsConfig $termsConfig;
    private NotificationFactory $notificationFactory;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        Context $context,
        TermsConfig $termsConfig,
        NotificationFactory $notificationFactory,
        NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($context);
        $this->termsConfig = $termsConfig;
        $this->notificationFactory = $notificationFactory;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $newVersion = trim((string)$this->getRequest()->getPostValue('version', ''));
        $title = trim((string)$this->getRequest()->getPostValue('title', ''));
        $message = trim((string)$this->getRequest()->getPostValue('message', ''));

        if (empty($newVersion)) {
            $this->messageManager->addErrorMessage(__('Por favor, informe a nova versão dos Termos de Uso.'));
            return $resultRedirect->setPath('*/*/index');
        }

        try {
            // 1. Atualiza a versão vigente nas configurações do sistema
            $this->termsConfig->setCurrentVersion($newVersion);

            // 2. Cria automaticamente a notificação global para todos os usuários (Ativa por padrão)
            $notificationTitle = !empty($title) ? $title : sprintf('Atualização dos Termos de Uso (%s)', $newVersion);
            $notificationMessage = !empty($message) ? $message : sprintf(
                'Publicamos uma nova versão dos Termos de Uso (%s). Por favor, acesse o link para ler e confirmar seu aceite.',
                $newVersion
            );

            $notification = $this->notificationFactory->create();
            $notification->setCustomerId(null); // Broadcast para todos
            $notification->setTitle($notificationTitle);
            $notification->setMessage($notificationMessage);
            $notification->setActionUrl('souenergy/terms/index');
            $notification->setActionLabel('Acessar Novos Termos →');
            $notification->setIsActive(true);
            $notification->setIsRead(false);

            $this->notificationRepository->save($notification);

            $this->messageManager->addSuccessMessage(
                __('Versão vigente dos Termos de Uso atualizada para "%1" com sucesso! Notificação enviada para todos os usuários comuns.', $newVersion)
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Erro ao atualizar termos: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
