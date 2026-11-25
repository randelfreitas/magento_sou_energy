<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Store\Model\ScopeInterface;

class TermsConfig
{
    public const XML_PATH_TERMS_VERSION = 'souenergy/terms/current_version';
    public const DEFAULT_TERMS_VERSION = 'v1.0';

    private ScopeConfigInterface $scopeConfig;
    private WriterInterface $configWriter;
    private TypeListInterface $cacheTypeList;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
    }

    public function getCurrentVersion(): string
    {
        $version = $this->scopeConfig->getValue(
            self::XML_PATH_TERMS_VERSION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return !empty($version) ? (string)$version : self::DEFAULT_TERMS_VERSION;
    }

    public function setCurrentVersion(string $version): void
    {
        $this->configWriter->save(
            self::XML_PATH_TERMS_VERSION,
            trim($version),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->cacheTypeList->cleanType('config');
    }
}
