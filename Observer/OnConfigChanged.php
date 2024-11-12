<?php

namespace GhoSter\MultipleSalesRecipient\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use GhoSter\MultipleSalesRecipient\Model\ResourceModel\UpdateAttribute as UpdateAttributeResource;

class OnConfigChanged implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UpdateAttributeResource
     */
    private $updateAttributeResource;

    /**
     * OnConfigChanged constructor.
     * @param RequestInterface $request
     * @param UpdateAttributeResource $updateAttributeResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        UpdateAttributeResource $updateAttributeResource,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->updateAttributeResource = $updateAttributeResource;
        $this->logger = $logger;
    }

    /**
     * Update comment flag
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $params = $this->request->getParam('groups');
        if (!isset($params['general']['fields']['limit_emails']['value'])) {
            return;
        }
        $limitNumber = (int)$params['general']['fields']['limit_emails']['value'];

        try {
            $this->updateAttributeResource->saveCommentFlag($limitNumber);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
