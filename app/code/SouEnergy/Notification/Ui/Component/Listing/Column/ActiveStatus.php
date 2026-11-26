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
                    $item[$fieldName] = '<span style="background: #ecfdf5; color: #047857; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px; display: inline-block;">✓ Ativa</span>';
                } else {
                    $item[$fieldName] = '<span style="background: #fee2e2; color: #991b1b; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px; display: inline-block;">✕ Inativa</span>';
                }
            }
        }

        return $dataSource;
    }
}
