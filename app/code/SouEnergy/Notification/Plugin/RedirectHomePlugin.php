<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Plugin;

use Magento\Cms\Controller\Index\Index;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\RedirectFactory;

class RedirectHomePlugin
{
    private CustomerSession $customerSession;
    private RedirectFactory $redirectFactory;

    public function __construct(
        CustomerSession $customerSession,
        RedirectFactory $redirectFactory
    ) {
        $this->customerSession = $customerSession;
        $this->redirectFactory = $redirectFactory;
    }

    public function aroundExecute(Index $subject, callable $proceed)
    {
        $redirect = $this->redirectFactory->create();

        if ($this->customerSession->isLoggedIn()) {
            return $redirect->setPath('customer/account');
        }

        return $redirect->setPath('customer/account/login');
    }
}
