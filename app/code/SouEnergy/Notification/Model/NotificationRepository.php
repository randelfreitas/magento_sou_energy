<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model;

use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Api\NotificationRepositoryInterface;
use SouEnergy\Notification\Model\NotificationFactory;
use SouEnergy\Notification\Model\ResourceModel\Notification as NotificationResource;

class NotificationRepository implements NotificationRepositoryInterface
{
    private NotificationResource $resource;
    private NotificationFactory $notificationFactory;

    public function __construct(
        NotificationResource $resource,
        NotificationFactory $notificationFactory
    ) {
        $this->resource = $resource;
        $this->notificationFactory = $notificationFactory;
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
