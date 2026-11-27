<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Block\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

class NotificationList extends Template
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

    public function getNotifications()
    {
        $customerId = (int)$this->customerSession->getCustomerId();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            [NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID],
            [
                ['eq' => $customerId],
                ['null' => true]
            ]
        );
        $collection->addFieldToFilter(NotificationInterface::IS_ACTIVE, 1);
        $collection->setOrder(NotificationInterface::CREATED_AT, 'DESC');
        return $collection->getItems();
    }

    public function getMarkAsReadUrl(int $id): string
    {
        return $this->getUrl('notificacoes/index/read', ['id' => $id]);
    }
}
