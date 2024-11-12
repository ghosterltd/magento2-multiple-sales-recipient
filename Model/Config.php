<?php

namespace GhoSter\MultipleSalesRecipient\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    public const ATTRIBUTE_CODE = 'multiple_sales_recipient';
    public const XML_PATH_ENABLED_MODULE = 'multi_sales_emails/general/enabled';
    public const XML_PATH_LIMIT_NUMBER = 'multi_sales_emails/general/limit_emails';
    public const DEFAULT_LIMIT_NUMBER = 9999;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if module enabled
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function isEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_ENABLED_MODULE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Limit number or email
     *
     * @param null|string|bool|int|Store $store
     * @return int
     */
    public function getLimitEmailNumber($store = null): int
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_LIMIT_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return empty($value) || $value == 0
            ? self::DEFAULT_LIMIT_NUMBER
            : (int)$value;
    }
}
