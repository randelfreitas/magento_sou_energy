<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Api;

use SouEnergy\Notification\Api\Data\NotificationInterface;

interface NotificationRepositoryInterface
{
    /**
     * Save notification
     *
     * @param NotificationInterface $notification
     * @return NotificationInterface
     */
    public function save(NotificationInterface $notification): NotificationInterface;

    /**
     * Get notification by ID
     *
     * @param int $entityId
     * @return NotificationInterface
     */
    public function getById(int $entityId): NotificationInterface;

    /**
     * Delete notification
     *
     * @param NotificationInterface $notification
     * @return bool
     */
    public function delete(NotificationInterface $notification): bool;

    /**
     * Delete notification by ID
     *
     * @param int $entityId
     * @return bool
     */
    public function deleteById(int $entityId): bool;
}
