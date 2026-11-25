<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model;

use Exception;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;
use SouEnergy\Notification\Model\NotificationFactory;
use SouEnergy\Notification\Model\ResourceModel\Notification as NotificationResource;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

class NotificationRepository implements NotificationRepositoryInterface
{
    private NotificationResource $resource;
    private NotificationFactory $notificationFactory;
    private NotificationCollectionFactory $collectionFactory;
    private UserContextInterface $userContext;

    public function __construct(
        NotificationResource $resource,
        NotificationFactory $notificationFactory,
        NotificationCollectionFactory $collectionFactory,
        UserContextInterface $userContext
    ) {
        $this->resource = $resource;
        $this->notificationFactory = $notificationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->userContext = $userContext;
    }

    public function save(NotificationInterface $notification): NotificationInterface
    {
        try {
            /** @var Notification $notification */
            $this->resource->save($notification);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $notification;
    }

    public function getById(int $entityId): NotificationInterface
    {
        /** @var Notification $notification */
        $notification = $this->notificationFactory->create();
        $this->resource->load($notification, $entityId);
        if (!$notification->getEntityId()) {
            throw new NoSuchEntityException(__('Notification with ID "%1" does not exist.', $entityId));
        }
        return $notification;
    }

    public function getMyNotifications(): array
    {
        $customerId = (int)$this->userContext->getUserId();

        $collection = $this->collectionFactory->create();
        // Target customer OR broadcast (customer_id IS NULL)
        $collection->addFieldToFilter(
            [NotificationInterface::CUSTOMER_ID, NotificationInterface::CUSTOMER_ID],
            [
                ['eq' => $customerId],
                ['null' => true]
            ]
        );
        $collection->setOrder(NotificationInterface::CREATED_AT, 'DESC');

        return $collection->getItems();
    }

    public function markAsRead(int $entityId): bool
    {
        $customerId = (int)$this->userContext->getUserId();
        $notification = $this->getById($entityId);

        $targetCustomerId = $notification->getCustomerId();
        if ($targetCustomerId !== null && $targetCustomerId !== $customerId) {
            throw new NoSuchEntityException(__('Notification does not belong to the authenticated user.'));
        }

        $notification->setIsRead(true);
        $this->save($notification);

        return true;
    }

    public function delete(NotificationInterface $notification): bool
    {
        try {
            /** @var Notification $notification */
            $this->resource->delete($notification);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }
}
