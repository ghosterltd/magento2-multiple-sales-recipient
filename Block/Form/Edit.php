<?php

namespace GhoSter\MultipleSalesRecipient\Block\Form;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Block\Form\Edit as CustomerFormEdit;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Newsletter\Model\SubscriberFactory;
use GhoSter\MultipleSalesRecipient\Model\Config;

class Edit extends CustomerFormEdit
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Edit constructor.
     * @param Config $config
     * @param Context $context
     * @param Session $customerSession
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param array $data
     */
    public function __construct(
        Config $config,
        Context $context,
        Session $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
    }

    /**
     * Get additional sales emails
     *
     * @return array
     */
    public function getAdditionalSalesEmails(): array
    {
        return array_map(
            'trim',
            explode(
                ',',
                $this->getAdditionalSalesEmailsInput()
            )
        );
    }

    /**
     * Get additional sales emails input
     *
     * @return string
     */
    public function getAdditionalSalesEmailsInput(): string
    {
        $customer = $this->getCustomer();

        if ($customer->getId() && $customer->getCustomAttribute(Config::ATTRIBUTE_CODE)) {
            return $customer->getCustomAttribute(Config::ATTRIBUTE_CODE)->getValue();
        }

        return '';
    }

    /**
     * Main configuration
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
