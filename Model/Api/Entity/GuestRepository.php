<?php

namespace Springbot\Main\Model\Api\Entity;

use Magento\Framework\Model\AbstractModel;
use Springbot\Main\Api\Entity\GuestRepositoryInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Springbot\Main\Model\Api\Entity\Data\GuestFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Request\Http;

/**
 *  GuestRepository
 * @package Springbot\Main\Api
 */
class GuestRepository extends AbstractRepository implements GuestRepositoryInterface
{
    /* @var GuestFactory $guestFactory */
    protected $guestFactory;

    /**
     * OrderRepository constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param \Springbot\Main\Model\Api\Entity\Data\GuestFactory $factory
     */
    public function __construct(
        Http $request,
        ResourceConnection $resourceConnection,
        ObjectManager $objectManager,
        GuestFactory $factory
    )
    {
        $this->guestFactory = $factory;
        parent::__construct($request, $resourceConnection, $objectManager);
    }

    public function getList($storeId)
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('sales_order')])
            ->where('store_id = ?', $storeId);
        $this->filterResults($select);

        $ret = [];
        foreach ($conn->fetchAll($select) as $row) {
            $ret[] = $this->createGuest($storeId, $row);
        }
        return $ret;
    }

    public function getFromId($storeId, $orderId)
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('sales_order')])
            ->where('entity_id = ?', $orderId);
        foreach ($conn->fetchAll($select) as $row) {
            return $this->createGuest($storeId, $row);
        }
        return null;
    }

    private function createGuest($storeId, $row)
    {
        $guest = $this->guestFactory->create();
        $guest->setValues(
            $storeId,
            $row['entity_id'],
            $row['customer_firstname'],
            $row['customer_lastname'],
            $row['customer_email'],
            $row['created_at'],
            $row['updated_at'],
            $row['billing_address_id'],
            $row['shipping_address_id']
        );
        return $guest;
    }
}