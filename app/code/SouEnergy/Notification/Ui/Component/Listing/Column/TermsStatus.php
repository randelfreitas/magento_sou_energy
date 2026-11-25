<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use SouEnergy\Notification\Model\TermsConfig;

class TermsStatus extends Column
{
    private TermsConfig $termsConfig;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TermsConfig $termsConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->termsConfig = $termsConfig;
    }

    public function prepare()
    {
        parent::prepare();
        $currentVersion = $this->termsConfig->getCurrentVersion();
        $config = $this->getData('config') ?: [];
        $config['label'] = sprintf('Termos de Uso (Vigente: %s)', $currentVersion);
        $this->setData('config', $config);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $currentVersion = $this->termsConfig->getCurrentVersion();
            $fieldName = $this->getName();

            foreach ($dataSource['data']['items'] as &$item) {
                $userVersion = !empty($item['terms_version']) ? (string)$item['terms_version'] : null;

                if (empty($userVersion)) {
                    $item[$fieldName] = '<span style="background: #fee2e2; color: #991b1b; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px; display: inline-block;">❌ Não Aceitou</span>';
                } elseif ($userVersion === $currentVersion) {
                    $item[$fieldName] = sprintf(
                        '<span style="background: #dcfce7; color: #166534; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px; display: inline-block;">✓ %s (Atualizado)</span>',
                        htmlspecialchars($userVersion)
                    );
                } else {
                    $item[$fieldName] = sprintf(
                        '<span style="background: #fef3c7; color: #92400e; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px; display: inline-block;">⚠️ Desatualizado (%s)</span>',
                        htmlspecialchars($userVersion)
                    );
                }
            }
        }

        return $dataSource;
    }
}
