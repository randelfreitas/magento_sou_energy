<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model\Notification;

use Magento\Ui\DataProvider\AbstractDataProvider;
use SouEnergy\Notification\Model\ResourceModel\Notification\CollectionFactory;

class GridDataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
