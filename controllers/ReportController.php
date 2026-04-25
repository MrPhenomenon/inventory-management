<?php

namespace app\controllers;

use app\models\InventoryTransactions;
use app\models\Parties;
use app\models\Payments;
use app\models\Products;
use app\models\Purchases;
use app\models\Sales;
use Yii;
use app\components\AppController;
use yii\data\ArrayDataProvider;

class ReportController extends AppController
{
 
    public function actionStock()
    {
        $products = Products::find()->all();
        $stockData = [];

        foreach ($products as $product) {
            $baseStock = $this->calculateBaseStock($product->id);
            $baseUnit = $product->baseUnit;

            $stockData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category ?: 'N/A',
                'base_stock' => $baseStock,
                'base_unit' => $baseUnit ? $baseUnit->name : 'N/A',
                'product' => $product,
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $stockData,
            'pagination' => ['pageSize' => 50],
            'sort' => ['attributes' => ['name', 'category', 'base_stock']],
        ]);

        return $this->render('stock', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReceivables()
    {
        $customers = Parties::find()
            ->where(['in', 'type', [Parties::TYPE_CUSTOMER, Parties::TYPE_BOTH]])
            ->all();

        $receivablesData = [];

        foreach ($customers as $customer) {
            $totalSales = Sales::find()->where(['customer_id' => $customer->id])->sum('total_amount') ?? 0;
            $totalPayments = Payments::find()
                ->where(['party_id' => $customer->id, 'type' => Payments::TYPE_INCOMING])
                ->sum('amount') ?? 0;

            $openingReceivable = $customer->opening_balance_type === 'receivable' ? (float)$customer->opening_balance : 0;
            $balance = (float)$totalSales - (float)$totalPayments + $openingReceivable;

            $receivablesData[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone ?: 'N/A',
                'total_sales' => $totalSales,
                'total_payments' => $totalPayments,
                'balance' => $balance,
                'party' => $customer,
            ];
        }

        $receivablesData = array_filter($receivablesData, function ($row) {
            return $row['balance'] > 0;
        });

        usort($receivablesData, function ($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        $dataProvider = new ArrayDataProvider([
            'allModels' => $receivablesData,
            'pagination' => ['pageSize' => 50],
        ]);

        $totalReceivables = array_sum(array_column($receivablesData, 'balance'));

        return $this->render('receivables', [
            'dataProvider' => $dataProvider,
            'totalReceivables' => $totalReceivables,
        ]);
    }

    public function actionPayables()
    {
        $suppliers = Parties::find()
            ->where(['in', 'type', [Parties::TYPE_SUPPLIER, Parties::TYPE_BOTH]])
            ->all();

        $payablesData = [];

        foreach ($suppliers as $supplier) {
            $totalPurchases = Purchases::find()->where(['supplier_id' => $supplier->id])->sum('total_amount') ?? 0;
            $totalPayments = Payments::find()
                ->where(['party_id' => $supplier->id, 'type' => Payments::TYPE_OUTGOING])
                ->sum('amount') ?? 0;

            $openingPayable = $supplier->opening_balance_type === 'payable' ? (float)$supplier->opening_balance : 0;
            $balance = (float)$totalPurchases - (float)$totalPayments + $openingPayable;

            $payablesData[] = [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'phone' => $supplier->phone ?: 'N/A',
                'total_purchases' => $totalPurchases,
                'total_payments' => $totalPayments,
                'balance' => $balance,
                'party' => $supplier,
            ];
        }

        $payablesData = array_filter($payablesData, function ($row) {
            return $row['balance'] > 0;
        });

        // Sort by balance descending
        usort($payablesData, function ($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        $dataProvider = new ArrayDataProvider([
            'allModels' => $payablesData,
            'pagination' => ['pageSize' => 50],
        ]);

        $totalPayables = array_sum(array_column($payablesData, 'balance'));

        return $this->render('payables', [
            'dataProvider' => $dataProvider,
            'totalPayables' => $totalPayables,
        ]);
    }

    public function actionProfitLoss()
    {
        $totalRevenue = Sales::find()->sum('total_amount') ?? 0;
        $totalCogs = Purchases::find()->sum('total_amount') ?? 0;
        $grossProfit = $totalRevenue - $totalCogs;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        return $this->render('profit-loss', [
            'totalRevenue' => $totalRevenue,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
        ]);
    }

    protected function calculateBaseStock($productId)
    {
        $tenantId = Yii::$app->user->identity->tenant_id;

        $sql = "SELECT COALESCE(SUM(CASE
                    WHEN type = :typeIn THEN base_quantity
                    WHEN type = :typeOut THEN -base_quantity
                    WHEN type = :typeAdjustment THEN base_quantity
                    ELSE 0
                END), 0) AS stock FROM inventory_transactions
                WHERE product_id = :product_id
                AND (:tenantId IS NULL OR tenant_id = :tenantId)";

        $stock = Yii::$app->db->createCommand($sql, [
            ':typeIn' => InventoryTransactions::TYPE_IN,
            ':typeOut' => InventoryTransactions::TYPE_OUT,
            ':typeAdjustment' => InventoryTransactions::TYPE_ADJUSTMENT,
            ':product_id' => $productId,
            ':tenantId' => $tenantId,
        ])->queryScalar();

        return (float)$stock;
    }
}
