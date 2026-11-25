<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model;

use Magento\Framework\Model\AbstractModel;
use SouEnergy\Notification\Api\Data\NotificationInterface;
use SouEnergy\Notification\Model\ResourceModel\Notification as NotificationResource;

class Notification extends AbstractModel implements NotificationInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(NotificationResource::class);
    }

    public function getEntityId(): ?int
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int)$id : null;
    }

    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getCustomerId(): ?int
    {
        $customerId = $this->getData(self::CUSTOMER_ID);
        return $customerId !== null ? (int)$customerId : null;
    }

    public function setCustomerId(?int $customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getTitle(): ?string
    {
        return (string)$this->getData(self::TITLE);
    }

    public function setTitle(string $title)
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getMessage(): ?string
    {
        return (string)$this->getData(self::MESSAGE);
    }

    public function setMessage(string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getIsRead(): bool
    {
        return (bool)$this->getData(self::IS_READ);
    }

    public function setIsRead(bool $isRead)
    {
        return $this->setData(self::IS_READ, $isRead ? 1 : 0);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT) ? (string)$this->getData(self::CREATED_AT) : null;
    }

    public function setCreatedAt(string $createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
