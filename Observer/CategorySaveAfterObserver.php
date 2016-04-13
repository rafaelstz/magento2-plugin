<?php

namespace Springbot\Main\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CategorySaveAfterObserver implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * CategorySaveAfterObserver constructor.
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(LoggerInterface $loggerInterface)
    {
        $this->logger = $loggerInterface;
    }

    /**
     * Pull the category data from the event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $category = $observer->getEvent()->getCategory();
            $this->logger->debug("Created/Updated Category ID: " . $category->getEntityId());
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
