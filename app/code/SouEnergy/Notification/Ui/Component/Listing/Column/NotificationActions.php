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
                    $isActive = array_key_exists('is_active_raw', $item)
                        ? (bool)$item['is_active_raw']
                        : (isset($item['is_active']) ? (bool)$item['is_active'] : true);

                    $item[$name]['toggle'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'souenergy_notification/notification/toggle',
                            ['entity_id' => $item['entity_id']]
                        ),
                        'label' => $isActive ? __('Desativar') : __('Ativar'),
                        'confirm' => [
                            'title' => $isActive ? __('Desativar Notificação') : __('Ativar Notificação'),
                            'message' => $isActive
                                ? __('Deseja desativar esta notificação? Ela não será mais exibida para os usuários.')
                                : __('Deseja ativar esta notificação? Ela voltará a ser exibida para os usuários.')
                        ]
                    ];

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
