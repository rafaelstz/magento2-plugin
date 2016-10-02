<?php

namespace Springbot\Main\Model\Api\Entity;

use Magento\Framework\Model\AbstractModel;
use Springbot\Main\Api\Entity\SubscriberRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ObjectManager;
use Springbot\Main\Model\Api\Entity\Data\SubscriberFactory;


/**
 * SubscriberRepository
 * @package Springbot\Main\Api
 */
class SubscriberRepository extends AbstractRepository implements SubscriberRepositoryInterface
{

    private $subscriberFactory;

    /**
     * OrderRepository constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param \Springbot\Main\Model\Api\Entity\Data\SubscriberFactory $factory
     */
    public function __construct(
        Http $request,
        ResourceConnection $resourceConnection,
        ObjectManager $objectManager,
        SubscriberFactory $factory
    )
    {
        $this->subscriberFactory = $factory;
        parent::__construct($request, $resourceConnection, $objectManager);
    }

    /**
     * @param int $storeId
     * @return \Springbot\Main\Api\Entity\Data\SubscriberInterface[]
     */
    public function getList($storeId)
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('newsletter_subscriber')])
            ->where('store_id = ?', $storeId);
        $this->filterResults($select);

        $ret = [];
        foreach ($conn->fetchAll($select) as $row) {
            $ret[] = $this->createSubscriber($storeId, $row);
        }
        return $ret;
    }

    /**
     * @param int $storeId
     * @param int $subscriberId
     * @return \Springbot\Main\Api\Entity\Data\SubscriberInterface
     */
    public function getFromId($storeId, $subscriberId)
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('newsletter_subscriber')])
            ->where('subscriber_id = ?', $subscriberId);

        foreach ($conn->fetchAll($select) as $row) {
            return $this->createSubscriber($storeId, $row);
        }
        return null;
    }

    /**
     * @param $storeId
     * @param $row
     * @return mixed
     */
    private function createSubscriber($storeId, $row)
    {
        $subscriber = $this->subscriberFactory->create();
        $subscriber->setValues(
            $storeId,
            $row['subscriber_status'],
            $row['subscriber_email'],
            $row['subscriber_id']
        );
        return $subscriber;
    }
}