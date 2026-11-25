<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Controller\Adminhtml\Terms;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Notification::notifications';

    private PageFactory $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SouEnergy_Notification::terms');
        $resultPage->getConfig()->getTitle()->prepend(__('Sou Energy - Gestão dos Termos de Uso'));
        return $resultPage;
    }
}
