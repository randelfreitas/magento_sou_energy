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
            // Atualiza a versão vigente e o conteúdo nas configurações do sistema
            $this->termsConfig->setCurrentVersion($newVersion);
            $this->termsConfig->setCurrentContent($content);

            // Cria automaticamente a notificação global para todos os usuários (Ativa por padrão)
            $notificationTitle = !empty($title) ? $title : sprintf('Atualização da Política de Privacidade (%s)', $newVersion);
            $notificationMessage = !empty($message) ? $message : sprintf(
                'Publicamos uma nova versão da Política de Privacidade (%s). Por favor, acesse o link para ler e confirmar seu aceite.',
                $newVersion
            );

            $notification = $this->notificationFactory->create();
            $notification->setCustomerId(null);
            $notification->setTitle($notificationTitle);
            $notification->setMessage($notificationMessage);
            $notification->setActionUrl('souenergy/terms/index');
            $notification->setActionLabel('Acessar Política de Privacidade →');
            $notification->setIsActive(true);
            $notification->setIsRead(false);

            $this->notificationRepository->save($notification);

            $this->messageManager->addSuccessMessage(
                __('Versão vigente da Política de Privacidade atualizada para "%1" com sucesso! Notificação enviada para todos os usuários comuns.', $newVersion)
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Erro ao atualizar a Política de Privacidade: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
