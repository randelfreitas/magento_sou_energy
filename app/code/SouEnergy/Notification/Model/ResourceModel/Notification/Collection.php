<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SouEnergy\Notification\Model\Notification as NotificationModel;
use SouEnergy\Notification\Model\ResourceModel\Notification as NotificationResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = NotificationResource::ID_FIELD_NAME;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(NotificationModel::class, NotificationResource::class);
    }
}
