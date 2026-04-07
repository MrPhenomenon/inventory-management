<?php

namespace app\components;

use app\models\InventoryTransactions;
use app\models\Products;
use Yii;

class SalesService
{
    /**
     * Get available stock in base units for a product.
     *
     * @param int $productId
     * @return float
     */
    public static function getAvailableStockByProduct(int $productId): float
    {
        $sql = "SELECT COALESCE(SUM(IF(type = :typeIn, base_quantity, 0)), 0) - COALESCE(SUM(IF(type = :typeOut, base_quantity, 0)), 0) AS available FROM inventory_transactions WHERE product_id = :product_id";
        $available = Yii::$app->db
            ->createCommand($sql, [
                ':typeIn' => InventoryTransactions::TYPE_IN,
                ':typeOut' => InventoryTransactions::TYPE_OUT,
                ':product_id' => $productId,
            ])
            ->queryScalar();

        return (float) $available;
    }

    /**
     * Validate requested sale items against available stock.
     *
     * @param array $items
     * @throws \Exception
     */
    public static function validateStockAvailability(array $items): void
    {
        $requestedBaseQuantities = [];

        foreach ($items as $item) {
            if (!isset($item['product_id'], $item['base_quantity'])) {
                continue;
            }

            $productId = (int) $item['product_id'];
            $requestedBaseQuantities[$productId] = ($requestedBaseQuantities[$productId] ?? 0) + (float) $item['base_quantity'];
        }

        foreach ($requestedBaseQuantities as $productId => $requiredBaseQuantity) {
            $availableBase = self::getAvailableStockByProduct($productId);
            if ($requiredBaseQuantity > $availableBase) {
                $product = Products::findOne($productId);
                $productName = $product ? $product->name : "Product #{$productId}";
                throw new \Exception("Insufficient stock for {$productName}. Requested base quantity: {$requiredBaseQuantity}, available: {$availableBase}.");
            }
        }
    }
}
