<?php

namespace app\controllers;

use app\models\Parties;
use app\models\Payments;
use app\models\PurchaseItems;
use app\models\Purchases;
use app\models\SaleItems;
use app\models\Sales;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PartyController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Parties.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Parties::find();
        $filter = Yii::$app->request->get('filter');

        if ($filter === 'customers') {
            $query->where(['in', 'type', [Parties::TYPE_CUSTOMER, Parties::TYPE_BOTH]]);
        } elseif ($filter === 'suppliers') {
            $query->where(['in', 'type', [Parties::TYPE_SUPPLIER, Parties::TYPE_BOTH]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filter' => $filter,
        ]);
    }

    /**
     * Displays a single Parties model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $totalSales = Sales::find()->where(['customer_id' => $id])->sum('total_amount') ?? 0;
        $totalPurchases = Purchases::find()->where(['supplier_id' => $id])->sum('total_amount') ?? 0;
        $totalIncomingPayments = Payments::find()->where(['party_id' => $id, 'type' => Payments::TYPE_INCOMING])->sum('amount') ?? 0;
        $totalOutgoingPayments = Payments::find()->where(['party_id' => $id, 'type' => Payments::TYPE_OUTGOING])->sum('amount') ?? 0;

        $openingBalance = (float)$model->opening_balance;
        $openingBalanceType = $model->opening_balance_type;

        $customerBalance = $totalSales - $totalIncomingPayments
            + ($openingBalanceType === Parties::OPENING_BALANCE_TYPE_RECEIVABLE ? $openingBalance : 0);
        $supplierBalance = $totalPurchases - $totalOutgoingPayments
            + ($openingBalanceType === Parties::OPENING_BALANCE_TYPE_PAYABLE ? $openingBalance : 0);

        $transactions = [];

        $sales = Sales::find()->where(['customer_id' => $id])->orderBy(['sale_date' => SORT_ASC])->all();
        foreach ($sales as $sale) {
            $transactions[] = [
                'date' => $sale->sale_date,
                'type' => 'Sale',
                'description' => $this->buildItemsDescription('sale', $sale->id),
                'debit' => $sale->total_amount,
                'credit' => 0,
                'status' => $sale->status,
                'sort_order' => 0,
            ];

            $salePayments = Payments::find()
                ->where(['party_id' => $id, 'reference_type' => 'sale', 'reference_id' => $sale->id])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
            
            foreach ($salePayments as $payment) {
                $transactions[] = [
                    'date' => $payment->created_at,
                    'type' => 'Payment (Incoming)',
                    'description' => '  ↳ Payment #' . $payment->id . ' - ' . ucfirst($payment->method),
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'status' => 'paid',
                    'sort_order' => 1,
                ];
            }
        }

        $purchases = Purchases::find()->where(['supplier_id' => $id])->orderBy(['purchase_date' => SORT_ASC])->all();
        foreach ($purchases as $purchase) {
            $transactions[] = [
                'date' => $purchase->purchase_date,
                'type' => 'Purchase',
                'description' => $this->buildItemsDescription('purchase', $purchase->id),
                'debit' => 0,
                'credit' => $purchase->total_amount,
                'status' => $purchase->status,
                'sort_order' => 0,
            ];

            $purchasePayments = Payments::find()
                ->where(['party_id' => $id, 'reference_type' => 'purchase', 'reference_id' => $purchase->id])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
            
            foreach ($purchasePayments as $payment) {
                $transactions[] = [
                    'date' => $payment->created_at,
                    'type' => 'Payment (Outgoing)',
                    'description' => '  ↳ Payment #' . $payment->id . ' - ' . ucfirst($payment->method),
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'status' => 'paid',
                    'sort_order' => 1,
                ];
            }
        }

        $linkedPaymentIds = [];
        $linkedPayments = Payments::find()
            ->where(['party_id' => $id])
            ->andWhere(['not', ['reference_id' => null]])
            ->select('id')
            ->column();
        $linkedPaymentIds = $linkedPayments;

        $orphanedPayments = Payments::find()
            ->where(['party_id' => $id])
            ->andWhere(['or', ['reference_id' => null], ['not in', 'id', $linkedPaymentIds]])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        foreach ($orphanedPayments as $payment) {
            if ($payment->type === Payments::TYPE_INCOMING) {
                $transactions[] = [
                    'date' => $payment->created_at,
                    'type' => 'Payment (Incoming)',
                    'description' => 'Payment #' . $payment->id . ' - ' . ucfirst($payment->method),
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'status' => 'paid',
                    'sort_order' => 0,
                ];
            } else {
                $transactions[] = [
                    'date' => $payment->created_at,
                    'type' => 'Payment (Outgoing)',
                    'description' => 'Payment #' . $payment->id . ' - ' . ucfirst($payment->method),
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'status' => 'paid',
                    'sort_order' => 0,
                ];
            }
        }

        usort($transactions, function ($a, $b) {
            $dateDiff = strtotime($a['date']) - strtotime($b['date']);
            if ($dateDiff === 0) {
                return $a['sort_order'] - $b['sort_order'];
            }
            return $dateDiff;
        });

        if ($model->hasOpeningBalance()) {
            array_unshift($transactions, [
                'date' => $model->created_at,
                'type' => 'Opening Balance',
                'description' => 'Opening Balance — ' . ($openingBalanceType === Parties::OPENING_BALANCE_TYPE_RECEIVABLE ? 'They owe us' : 'We owe them'),
                'debit' => $openingBalanceType === Parties::OPENING_BALANCE_TYPE_RECEIVABLE ? $openingBalance : 0,
                'credit' => $openingBalanceType === Parties::OPENING_BALANCE_TYPE_PAYABLE ? $openingBalance : 0,
                'status' => 'opening',
                'sort_order' => -1,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalIncomingPayments' => $totalIncomingPayments,
            'totalOutgoingPayments' => $totalOutgoingPayments,
            'customerBalance' => $customerBalance,
            'supplierBalance' => $supplierBalance,
            'openingBalance' => $openingBalance,
            'openingBalanceType' => $openingBalanceType,
            'transactions' => $transactions,
        ]);
    }

    public function actionCreate()
    {
        $model = new Parties();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Party created successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Party updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Party deleted successfully.');
        return $this->redirect(['index']);
    }

 
    /**
     * Build a human-readable description for a sale or purchase from its items.
     * Format: "Product - Unit × Qty, Product - Unit × Qty, ..."
     * Falls back to "Sale #id" / "Purchase #id" if items are missing.
     */
    private function buildItemsDescription(string $type, int $refId): string
    {
        if ($type === 'sale') {
            $items = SaleItems::find()
                ->with(['product', 'productUnit'])
                ->where(['sale_id' => $refId])
                ->all();
            $fallback = 'Sale #' . $refId;
        } else {
            $items = PurchaseItems::find()
                ->with(['product', 'productUnit'])
                ->where(['purchase_id' => $refId])
                ->all();
            $fallback = 'Purchase #' . $refId;
        }

        if (empty($items)) {
            return $fallback;
        }

        $parts = [];
        foreach ($items as $item) {
            $productName = $item->product ? $item->product->name : 'Product #' . $item->product_id;
            $unitName    = $item->productUnit ? $item->productUnit->unit_name : '';
            $qty         = rtrim(rtrim(number_format((float)$item->quantity, 3, '.', ''), '0'), '.');

            $parts[] = $unitName
                ? $productName . ' - ' . $unitName . ' × ' . $qty
                : $productName . ' × ' . $qty;
        }

        return implode(', ', $parts);
    }

    protected function findModel($id)
    {
        if (($model = Parties::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested party does not exist.');
    }
}
