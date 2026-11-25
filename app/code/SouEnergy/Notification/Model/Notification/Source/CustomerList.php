<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model\Notification\Source;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerList implements OptionSourceInterface
{
    private CustomerCollectionFactory $customerCollectionFactory;

    public function __construct(CustomerCollectionFactory $customerCollectionFactory)
    {
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $options = [
            ['value' => '', 'label' => __('-- Todos os Usuários (Geral) --')]
        ];

        try {
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addAttributeToSelect(['firstname', 'lastname', 'email']);

            foreach ($customerCollection as $customer) {
                $options[] = [
                    'value' => (int)$customer->getId(),
                    'label' => sprintf('%s %s (%s)', $customer->getFirstname(), $customer->getLastname(), $customer->getEmail())
                ];
            }
        } catch (\Exception $e) {
            // Em caso de tabela de clientes vazia ou inicial
        }

        return $options;
    }
}
