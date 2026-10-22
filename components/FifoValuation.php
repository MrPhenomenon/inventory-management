<?php

namespace app\components;

use app\models\InventoryTransactions;
use Yii;

class FifoValuation
{
    public const NONE = ['value' => 0.0, 'uncosted_qty' => 0.0, 'lots' => []];

    private const EPSILON = 0.000001;

    public static function compute(): array
    {
        $sql = "SELECT t.product_id, t.type, t.base_quantity,
                    CASE WHEN t.reference_type = 'purchase' THEN (
                        SELECT SUM(pi.total) / NULLIF(SUM(pi.quantity * pu.conversion_to_base), 0)
                        FROM purchase_items pi
                        JOIN product_units pu ON pu.id = pi.product_unit_id
                        WHERE pi.purchase_id = t.reference_id
                          AND pi.product_id = t.product_id
                          AND pi.product_unit_id = t.product_unit_id
                    ) END AS unit_cost
                FROM inventory_transactions t
                ORDER BY t.created_at, t.id";

        $transactionsByProduct = [];
        foreach (Yii::$app->db->createCommand($sql)->queryAll() as $row) {
            $transactionsByProduct[(int) $row['product_id']][] = $row;
        }

        $valuations = [];
        foreach ($transactionsByProduct as $productId => $transactions) {
            $valuations[$productId] = self::replay($transactions);
        }

        return $valuations;
    }

    private static function replay(array $transactions): array
    {
        $lots = [];
        $deficit = 0.0;

        foreach ($transactions as $transaction) {
            $quantity = (float) $transaction['base_quantity'];
            $isOutflow = $transaction['type'] === InventoryTransactions::TYPE_OUT
                || ($transaction['type'] === InventoryTransactions::TYPE_ADJUSTMENT && $quantity < 0);

            if ($isOutflow) {
                $deficit += self::consume($lots, abs($quantity));
                continue;
            }

            $covered = min($deficit, $quantity);
            $deficit -= $covered;
            $quantity -= $covered;

            if ($quantity > self::EPSILON) {
                $lots[] = [
                    'qty' => $quantity,
                    'cost' => $transaction['unit_cost'] === null ? null : (float) $transaction['unit_cost'],
                ];
            }
        }

        return self::summarize($lots);
    }

    private static function consume(array &$lots, float $quantity): float
    {
        while ($quantity > self::EPSILON && $lots) {
            $taken = min($quantity, $lots[0]['qty']);
            $lots[0]['qty'] -= $taken;
            $quantity -= $taken;

            if ($lots[0]['qty'] <= self::EPSILON) {
                array_shift($lots);
            }
        }

        return $quantity > self::EPSILON ? $quantity : 0.0;
    }

    private static function summarize(array $lots): array
    {
        $value = 0.0;
        $uncostedQty = 0.0;
        $grouped = [];

        foreach ($lots as $lot) {
            if ($lot['cost'] === null) {
                $uncostedQty += $lot['qty'];
            } else {
                $value += $lot['qty'] * $lot['cost'];
            }

            $last = count($grouped) - 1;
            if ($last >= 0 && $grouped[$last]['cost'] === $lot['cost']) {
                $grouped[$last]['qty'] += $lot['qty'];
            } else {
                $grouped[] = $lot;
            }
        }

        return ['value' => $value, 'uncosted_qty' => $uncostedQty, 'lots' => $grouped];
    }
}
