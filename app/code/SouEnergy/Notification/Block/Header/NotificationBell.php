<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Block\Header;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

class NotificationBell extends Template
{
    private CustomerSession $customerSession;
    private NotificationCollectionFactory $collectionFactory;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        NotificationCollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
    }

    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getUnreadCount(): int
    {
        if (!$this->isLoggedIn()) {
            return 0;
        }

        $customerId = (int)$this->customerSession->getCustomerId();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            [NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID],
            [
                ['eq' => $customerId],
                ['null' => true]
            ]
        );
        $collection->addFieldToFilter(NotificationInterface::IS_READ, 0);
        $collection->addFieldToFilter(NotificationInterface::IS_ACTIVE, 1);

        return $collection->getSize();
    }

    public function getNotificationsUrl(): string
    {
        return $this->getUrl('notificacoes/index/index');
    }
}
