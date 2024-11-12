<?php

namespace GhoSter\MultipleSalesRecipient\Plugin\Magento\Framework\Mail\Template;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;
use GhoSter\MultipleSalesRecipient\Model\Config;

class TransportBuilderPlugin
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $customerEmails = [];

    /**
     * TransportBuilderPlugin constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Get current email addresses
     *
     * @param TransportBuilder $subject
     * @param array|string $address
     * @param mixed $name
     * @return array
     */
    public function beforeAddTo(TransportBuilder $subject, $address, $name = ''): array
    {
        if (!$this->config->isEnabled()) {
            return [$address, $name];
        }

        if (is_string($address)) {
            $this->customerEmails[] = $address;
        } elseif (is_array($address)) {
            $this->customerEmails = array_merge($this->customerEmails, $address);
        }

        $this->customerEmails = array_unique($this->customerEmails);

        return [$address, $name];
    }

    /**
     * Add customer email cc
     *
     * @param TransportBuilder $subject
     * @return array
     */
    public function beforeGetTransport(
        TransportBuilder $subject
    ): array {

        if (!$this->config->isEnabled()) {
            return [];
        }

        try {
            $ccEmailAddresses = $this->getEmailCopyTo();
            if (empty($ccEmailAddresses)) {
                return [];
            }

            foreach ($ccEmailAddresses as $ccEmailAddress) {
                $subject->addCc(trim($ccEmailAddress));
                $this->logger->debug((string)__('Added customer CC: %1', trim($ccEmailAddress)));
            }
        } catch (\Exception $e) {
            $this->logger->error((string)__('Failure to add customer CC: %1', $e->getMessage()));
        }

        return [];
    }

    /**
     * Return email copy_to list
     *
     * @return array
     */
    protected function getEmailCopyTo(): array
    {
        $ccEmailAddresses = [];
        if (empty($this->customerEmails)) {
            return $ccEmailAddresses;
        }

        foreach ($this->customerEmails as $customerEmail) {
            try {
                $customer = $this->customerRepositoryInterface->get($customerEmail);
            } catch (NoSuchEntityException|LocalizedException $e) {
                continue;
            }

            $emailCc = $customer->getCustomAttribute(Config::ATTRIBUTE_CODE);
            $customerEmailCC = $emailCc ? $emailCc->getValue() : null;

            if (!empty($customerEmailCC)) {
                //phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                return array_merge($ccEmailAddresses, explode(',', trim($customerEmailCC)));
            }
        }

        return array_unique($ccEmailAddresses);
    }
}
