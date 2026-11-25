<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Api\Data;

interface NotificationInterface
{
    public const ENTITY_ID = 'entity_id';
    public const CUSTOMER_ID = 'customer_id';
    public const TITLE = 'title';
    public const MESSAGE = 'message';
    public const IS_READ = 'is_read';
    public const CREATED_AT = 'created_at';

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * @param int|null $customerId
     * @return $this
     */
    public function setCustomerId(?int $customerId);

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title);

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message);

    /**
     * @return bool
     */
    public function getIsRead(): bool;

    /**
     * @param bool $isRead
     * @return $this
     */
    public function setIsRead(bool $isRead);

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt);
}
