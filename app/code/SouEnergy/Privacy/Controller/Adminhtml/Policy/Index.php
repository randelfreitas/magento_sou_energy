<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Controller\Adminhtml\Policy;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'SouEnergy_Privacy::manage';

    private PageFactory $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SouEnergy_Privacy::policy');
        $resultPage->getConfig()->getTitle()->prepend(__('Gerenciar Política de Privacidade'));
        return $resultPage;
    }
}
