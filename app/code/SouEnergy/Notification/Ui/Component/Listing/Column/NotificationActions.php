<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class NotificationActions extends Column
{
    private UrlInterface $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'souenergy_notification/notification/delete',
                            ['entity_id' => $item['entity_id']]
                        ),
                        'label' => __('Excluir'),
                        'confirm' => [
                            'title' => __('Excluir Notificação'),
                            'message' => __('Tem certeza de que deseja excluir esta notificação?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
