<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Notification extends AbstractDb
{
    public const TABLE_NAME = 'souenergy_notification';
    public const ID_FIELD_NAME = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
