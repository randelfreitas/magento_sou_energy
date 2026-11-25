<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Setup\Patch\Data;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;
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
    private CustomerRegistry $customerRegistry;
    private EncryptorInterface $encryptor;
    private StoreManagerInterface $storeManager;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerFactory,
        CustomerRegistry $customerRegistry,
        EncryptorInterface $encryptor,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
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

            $passwordHash = $this->encryptor->getHash(self::DEFAULT_PASSWORD, true);
            $savedCustomer = $this->customerRepository->save($customer, $passwordHash);

            $customerModel = $this->customerRegistry->retrieveSecureData((int)$savedCustomer->getId());
            $customerModel->setPasswordHash($passwordHash);
        } catch (Exception $e) {
            // Fallback caso ocorra alguma exceção não crítica durante patch inicial
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
