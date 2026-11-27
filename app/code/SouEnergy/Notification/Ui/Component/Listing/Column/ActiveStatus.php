<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class ActiveStatus extends Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getName();
            foreach ($dataSource['data']['items'] as &$item) {
                $isActive = isset($item[$fieldName]) ? (bool)$item[$fieldName] : true;
                if ($isActive) {
                    $item[$fieldName] = '<span class="grid-severity-notice"><span>Ativa</span></span>';
                } else {
                    $item[$fieldName] = '<span class="grid-severity-critical"><span>Inativa</span></span>';
                }
            }
        }

        return $dataSource;
    }
}
