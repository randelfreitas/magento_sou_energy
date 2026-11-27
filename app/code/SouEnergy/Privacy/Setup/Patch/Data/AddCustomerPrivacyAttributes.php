<?php
declare(strict_types=1);

namespace SouEnergy\Privacy\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SouEnergy\Portal\Setup\Patch\Data\CreateDefaultCustomer;

class AddCustomerPrivacyAttributes implements DataPatchInterface
{
    public const ATTR_VERSION = 'privacy_policy_version';
    public const ATTR_ACCEPTED_AT = 'privacy_policy_accepted_at';

    private ModuleDataSetupInterface $moduleDataSetup;
    private CustomerSetupFactory $customerSetupFactory;
    private AttributeSetFactory $attributeSetFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, self::ATTR_VERSION, [
            'type' => 'varchar',
            'label' => 'Versão Política Aceita',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 900,
            'position' => 900,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true,
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, self::ATTR_ACCEPTED_AT, [
            'type' => 'datetime',
            'label' => 'Data Aceite Política',
            'input' => 'date',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 910,
            'position' => 910,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => false,
        ]);

        foreach ([self::ATTR_VERSION, self::ATTR_ACCEPTED_AT] as $attributeCode) {
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode);
            $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
            $attribute->save();
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    public static function getDependencies(): array
    {
        return [
            CreateDefaultCustomer::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }
}
