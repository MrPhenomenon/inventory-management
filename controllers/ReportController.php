<?php

namespace app\controllers;

use app\models\Accounts;
use app\models\ChemicalAccount;
use app\models\ExpenseCategories;
use app\models\Expenses;
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

            $openingReceivable = $customer->opening_balance_type === 'receivable' ? (float) $customer->opening_balance : 0;
            $balance = (float) $totalSales - (float) $totalPayments + $openingReceivable;

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

            $openingPayable = $supplier->opening_balance_type === 'payable' ? (float) $supplier->opening_balance : 0;
            $balance = (float) $totalPurchases - (float) $totalPayments + $openingPayable;

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

    public function actionBalanceSheet()
    {
      
        $chemicalBalance = ChemicalAccount::calculateBalance();

        $accounts = Accounts::find()->orderBy(['name' => SORT_ASC])->all();
        $accountRows = [];
        $totalAccounts = 0;
        foreach ($accounts as $acc) {
            $bal = $acc->getBalance();
            $accountRows[] = ['name' => $acc->name, 'balance' => $bal];
            $totalAccounts += $bal;
        }

        // --- Receivables: customer-only parties ---
        $customers = Parties::find()->where(['type' => Parties::TYPE_CUSTOMER])->all();
        $receivableRows = [];
        $totalReceivables = 0;
        foreach ($customers as $c) {
            $totalSales = (float)(Sales::find()->where(['customer_id' => $c->id])->sum('total_amount') ?? 0);
            $totalPaid  = (float)(Payments::find()->where(['party_id' => $c->id, 'type' => Payments::TYPE_INCOMING])->sum('amount') ?? 0);
            $opening    = $c->opening_balance_type === 'receivable' ? (float)$c->opening_balance : 0;
            $bal        = $totalSales - $totalPaid + $opening;
            if ($bal > 0) {
                $receivableRows[] = ['name' => $c->name, 'balance' => $bal];
                $totalReceivables += $bal;
            }
        }

        // --- Payables: supplier-only parties ---
        $suppliers = Parties::find()->where(['type' => Parties::TYPE_SUPPLIER])->all();
        $payableRows = [];
        $totalPayables = 0;
        foreach ($suppliers as $s) {
            $totalPurchases = (float)(Purchases::find()->where(['supplier_id' => $s->id])->sum('total_amount') ?? 0);
            $totalPaid      = (float)(Payments::find()->where(['party_id' => $s->id, 'type' => Payments::TYPE_OUTGOING])->sum('amount') ?? 0);
            $opening        = $s->opening_balance_type === 'payable' ? (float)$s->opening_balance : 0;
            $bal            = $totalPurchases - $totalPaid + $opening;
            if ($bal > 0) {
                $payableRows[] = ['name' => $s->name, 'balance' => $bal];
                $totalPayables += $bal;
            }
        }

        // --- Both parties: net receivable side vs payable side ---
        $bothParties = Parties::find()->where(['type' => Parties::TYPE_BOTH])->all();
        foreach ($bothParties as $p) {
            $totalSales     = (float)(Sales::find()->where(['customer_id' => $p->id])->sum('total_amount') ?? 0);
            $incomingPaid   = (float)(Payments::find()->where(['party_id' => $p->id, 'type' => Payments::TYPE_INCOMING])->sum('amount') ?? 0);
            $openingRec     = $p->opening_balance_type === 'receivable' ? (float)$p->opening_balance : 0;
            $receivableSide = $totalSales - $incomingPaid + $openingRec;

            $totalPurchases = (float)(Purchases::find()->where(['supplier_id' => $p->id])->sum('total_amount') ?? 0);
            $outgoingPaid   = (float)(Payments::find()->where(['party_id' => $p->id, 'type' => Payments::TYPE_OUTGOING])->sum('amount') ?? 0);
            $openingPay     = $p->opening_balance_type === 'payable' ? (float)$p->opening_balance : 0;
            $payableSide    = $totalPurchases - $outgoingPaid + $openingPay;

            $net = $receivableSide - $payableSide;

            if ($net > 0) {
                $receivableRows[] = ['name' => $p->name, 'balance' => $net];
                $totalReceivables += $net;
            } elseif ($net < 0) {
                $payableRows[] = ['name' => $p->name, 'balance' => abs($net)];
                $totalPayables += abs($net);
            }
        }

        $categories = ExpenseCategories::find()->orderBy(['name' => SORT_ASC])->all();
        $expenseRows = [];
        $totalExpenses = 0;
        foreach ($categories as $cat) {
            $pending = Expenses::currentTotal($cat->id);
            if ($pending > 0) {
                $expenseRows[] = ['name' => $cat->name, 'balance' => $pending];
                $totalExpenses += $pending;
            }
        }

        $totalSide1    = $chemicalBalance + $totalAccounts + $totalReceivables + $totalExpenses;
        $totalSide2    = $totalPayables;
        $netDifference = $totalSide1 - $totalSide2;

        return $this->render('balance-sheet', [
            'chemicalBalance'  => $chemicalBalance,
            'accountRows'      => $accountRows,
            'totalAccounts'    => $totalAccounts,
            'receivableRows'   => $receivableRows,
            'totalReceivables' => $totalReceivables,
            'expenseRows'      => $expenseRows,
            'totalExpenses'    => $totalExpenses,
            'totalSide1'       => $totalSide1,
            'payableRows'      => $payableRows,
            'totalPayables'    => $totalPayables,
            'totalSide2'       => $totalSide2,
            'netDifference'    => $netDifference,
        ]);
    }

    protected function calculateBaseStock($productId)
    {
        $sql = "SELECT COALESCE(SUM(CASE
                    WHEN type = :typeIn THEN base_quantity
                    WHEN type = :typeOut THEN -base_quantity
                    WHEN type = :typeAdjustment THEN base_quantity
                    ELSE 0
                END), 0) AS stock FROM inventory_transactions
                WHERE product_id = :product_id";

        $stock = Yii::$app->db->createCommand($sql, [
            ':typeIn' => InventoryTransactions::TYPE_IN,
            ':typeOut' => InventoryTransactions::TYPE_OUT,
            ':typeAdjustment' => InventoryTransactions::TYPE_ADJUSTMENT,
            ':product_id' => $productId,
        ])->queryScalar();

        return (float) $stock;
    }
}
