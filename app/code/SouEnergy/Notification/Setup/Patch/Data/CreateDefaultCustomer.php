<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Setup\Patch\Data;

use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateDefaultCustomer implements DataPatchInterface
{
    public const DEFAULT_EMAIL = 'randel@souenergy.com.br';
    public const DEFAULT_FIRSTNAME = 'Marcos';
    public const DEFAULT_LASTNAME = 'Randel';
    public const DEFAULT_PASSWORD = 'UserPassword123*';

    private CustomerRepositoryInterface $customerRepository;
    private CustomerInterfaceFactory $customerFactory;
    private AccountManagementInterface $accountManagement;
    private StoreManagerInterface $storeManager;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerFactory,
        AccountManagementInterface $accountManagement,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->storeManager = $storeManager;
    }

    public function apply()
    {
        try {
            $websiteId = (int)$this->storeManager->getWebsite()->getId();
            $this->customerRepository->get(self::DEFAULT_EMAIL, $websiteId);
        } catch (NoSuchEntityException $e) {
            $websiteId = (int)$this->storeManager->getWebsite()->getId();
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->setEmail(self::DEFAULT_EMAIL);
            $customer->setFirstname(self::DEFAULT_FIRSTNAME);
            $customer->setLastname(self::DEFAULT_LASTNAME);

            // createAccount grava a senha com o hash e salt oficial do Magento para autenticação perfeita
            $this->accountManagement->createAccount($customer, self::DEFAULT_PASSWORD);
        } catch (Exception $e) {
            // Tratamento silencioso
        }

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
