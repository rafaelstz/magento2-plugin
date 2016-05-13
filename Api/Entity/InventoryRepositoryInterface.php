<?php

namespace Springbot\Main\Api\Entity;

/**
 * Interface InventoryRepositoryInterface
 * @package Springbot\Main\Api
 */
interface InventoryRepositoryInterface
{
    /**
     * Get store configuration
     *
     * @param int $storeId
     * @return \Springbot\Main\Api\Data\InventoryInterface[]
     */
    public function getList($storeId);

    /**
     * @param int $storeId
     * @param int $inventoryId
     * @return \Springbot\Main\Api\Data\InventoryInterface
     */
    public function getFromId($storeId, $inventoryId);

}
