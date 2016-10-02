<?php

namespace Springbot\Main\Model\Api\Entity\Data;

use Magento\Framework\App\ResourceConnection;
use Springbot\Main\Api\Entity\Data\OrderInterface;
use Springbot\Main\Model\Api\Entity\Data\Order\ItemFactory;

/**
 * Class Order
 * @package Springbot\Main\Model\Api\Entity\Data
 */
class Order implements OrderInterface
{
    public $storeId;
    public $incrementId;
    public $orderId;
    public $customerEmail;
    public $quoteId;
    public $redirectMongoId;
    public $redirectMongoIds;
    public $customerId;
    public $grandTotal;
    public $remoteIp;
    public $status;
    public $state;
    public $customerIsGuest;
    public $createdAt;
    public $discountAmount;
    public $totalPaid;
    public $shippingMethod;
    public $shippingAmount;
    public $shipments;
    public $couponCode;
    public $orderCurrencyCode;
    public $baseTaxAmount;
    public $cartUserAgent;
    public $orderUserAgent;

    private $resourceConnection;
    private $itemFactory;

    public function __construct(ResourceConnection $productRepository, ItemFactory $factory)
    {
        $this->resourceConnection = $productRepository;
        $this->itemFactory = $factory;
    }

    /**
     * @param $storeId
     * @param $incrementId
     * @param $orderId
     * @param $customerEmail
     * @param $quoteId
     * @param $customerId
     * @param $grandTotal
     * @param $remoteIp
     * @param $status
     * @param $state
     * @param $customerIsGuest
     * @param $createdAt
     * @param $discountAmount
     * @param $totalPaid
     * @param $shippingMethod
     * @param $shippingAmount
     * @param $couponCode
     * @param $orderCurrencyCode
     * @param $baseTaxAmount
     * @return void
     */
    public function setValues(
        $storeId,
        $incrementId,
        $orderId,
        $customerEmail,
        $quoteId,
        $customerId,
        $grandTotal,
        $remoteIp,
        $status,
        $state,
        $customerIsGuest,
        $createdAt,
        $discountAmount,
        $totalPaid,
        $shippingMethod,
        $shippingAmount,
        $couponCode,
        $orderCurrencyCode,
        $baseTaxAmount
    ) {
        $this->storeId = $storeId;
        $this->incrementId = $incrementId;
        $this->orderId = $orderId;
        $this->customerEmail = $customerEmail;
        $this->quoteId = $quoteId;
        $this->customerId = $customerId;
        $this->grandTotal = $grandTotal;
        $this->remoteIp = $remoteIp;
        $this->status = $status;
        $this->state = $state;
        $this->customerIsGuest = $customerIsGuest;
        $this->createdAt = $createdAt;
        $this->discountAmount = $discountAmount;
        $this->totalPaid = $totalPaid;
        $this->shippingMethod = $shippingMethod;
        $this->shippingAmount = $shippingAmount;
        $this->couponCode = $couponCode;
        $this->orderCurrencyCode = $orderCurrencyCode;
        $this->baseTaxAmount = $baseTaxAmount;
    }

    /**
     * @return mixed
     */
    public function getIncrementId()
    {
        return $this->incrementId;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @return mixed
     */
    public function getQuoteId()
    {
        return $this->quoteId;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return mixed
     */
    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    /**
     * @return mixed
     */
    public function getRemoteIp()
    {
        return $this->remoteIp;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getCustomerIsGuest()
    {
        return $this->customerIsGuest;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @return mixed
     */
    public function getTotalPaid()
    {
        return $this->totalPaid;
    }

    /**
     * @return mixed
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @return mixed
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * @return mixed
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('sales_order_payment')])
            ->where('entity_id = ?', $this->orderId);
        foreach ($conn->fetchAll($select) as $payment) {
            return $payment['method'];
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @return mixed
     */
    public function getOrderCurrencyCode()
    {
        return $this->orderCurrencyCode;
    }

    /**
     * @return mixed
     */
    public function getBaseTaxAmount()
    {
        return $this->baseTaxAmount;
    }

    public function getRedirectMongoId()
    {
        $redirects = $this->getRedirectMongoIds();
        return end($redirects);
    }

    public function getRedirectMongoIds()
    {
        if (isset($this->redirectMongoIds)) {
            return $this->redirectMongoIds;
        }
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('springbot_order_redirect')])
            ->where('order_id = ?', $this->orderId)
            ->order('id', 'DESC');

        $redirectIds = [];
        foreach ($conn->fetchAll($select) as $row) {
            $redirectIds[] = $row['redirect_string'];
        }
        $this->redirectMongoIds = $redirectIds;
        return $this->redirectMongoIds;
    }

    /**
     * @return \Springbot\Main\Api\Entity\Data\Order\ItemInterface[];
     */
    public function getItems()
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from(['soi' => $conn->getTableName('sales_order_item')])
            ->joinLeft(
                ['soip' => $conn->getTableName('sales_order_item')],
                'soi.parent_item_id = soip.item_id',
                ['soip.product_id AS parent_product_id', 'soip.sku AS parent_sku']
            )
            ->where('soi.order_id = ?', $this->orderId);

        $ret = [];
        foreach ($conn->fetchAll($select) as $row) {
            $item = $this->itemFactory->create();
            /* @var \Springbot\Main\Model\Api\Entity\Data\Order\Item $item */

            if ($row['parent_sku']) {
                $parentSku = $row['parent_sku'];
            }
            else {
                $parentSku = $row['sku'];
            }

            $item->setValues(
                $this->storeId,
                $parentSku,
                $row['sku'],
                $row['qty_ordered'],
                $row['weight'],
                $row['name'],
                $row['price'],
                $row['product_id'],
                $row['parent_product_id'],
                $row['product_type']
            );
            $ret[] = $item;
        }
        return $ret;
    }


    public function getCartUserAgent()
    {
        return $this->fetchTrackable('quote_id', $this->quoteId, 'cart_user_agent');
    }

    public function getOrderUserAgent()
    {
        return $this->fetchTrackable('order_id', $this->orderId, 'order_user_agent');
    }

    private function fetchTrackable($column, $value, $type)
    {
        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from([$conn->getTableName('springbot_trackable')])
            ->where($column . ' = ?', $value)
            ->where('type = ?', $type)
            ->order('id', 'DESC');
        foreach ($conn->fetchAll($select) as $row) {
            return $row['value'];
        }
    }

}
