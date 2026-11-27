<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use SouEnergy\Privacy\Model\PolicyConfig;
use SouEnergy\Privacy\Setup\Patch\Data\AddCustomerPrivacyAttributes;

class PolicyStatus extends Column
{
    private PolicyConfig $policyConfig;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PolicyConfig $policyConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->policyConfig = $policyConfig;
    }

    public function prepare()
    {
        parent::prepare();
        $currentVersion = $this->policyConfig->getCurrentVersion();
        $config = $this->getData('config') ?: [];
        $config['label'] = sprintf('Política de Privacidade (Vigente: %s)', $currentVersion);
        $this->setData('config', $config);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $currentVersion = $this->policyConfig->getCurrentVersion();
            $fieldName = $this->getName();
            $attrKey = AddCustomerPrivacyAttributes::ATTR_VERSION;

            foreach ($dataSource['data']['items'] as &$item) {
                $userVersion = !empty($item[$attrKey]) ? (string)$item[$attrKey] : null;

                if (empty($userVersion)) {
                    $item[$fieldName] = '<span class="grid-severity-critical"><span>Não Aceitou</span></span>';
                } elseif ($userVersion === $currentVersion) {
                    $item[$fieldName] = sprintf(
                        '<span class="grid-severity-notice"><span>%s (Atualizado)</span></span>',
                        htmlspecialchars($userVersion)
                    );
                } else {
                    $item[$fieldName] = sprintf(
                        '<span class="grid-severity-minor"><span>Desatualizado (%s)</span></span>',
                        htmlspecialchars($userVersion)
                    );
                }
            }
        }

        return $dataSource;
    }
}
