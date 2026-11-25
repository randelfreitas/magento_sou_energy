<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Ui\Component\Listing\Column;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class CustomerName extends Column
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerRepositoryInterface $customerRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerRepository = $customerRepository;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (empty($item['customer_id'])) {
                    $item[$this->getData('name')] = (string)__('Todos os Usuários');
                } else {
                    try {
                        $customer = $this->customerRepository->getById((int)$item['customer_id']);
                        $item[$this->getData('name')] = sprintf('%s %s (%s)', $customer->getFirstname(), $customer->getLastname(), $customer->getEmail());
                    } catch (\Exception $e) {
                        $item[$this->getData('name')] = (string)__('Usuário #%1', $item['customer_id']);
                    }
                }
            }
        }

        return $dataSource;
    }
}
