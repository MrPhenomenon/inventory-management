<?php

namespace app\controllers;

use app\models\Payments;
use app\models\Parties;
use app\models\Sales;
use app\models\Purchases;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PaymentController extends Controller
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

    public function actionIndex()
    {
        $query = Payments::find();

        $partyId = Yii::$app->request->get('party_id');
        if ($partyId) {
            $query->where(['party_id' => $partyId]);
        }

        $type = Yii::$app->request->get('type');
        if ($type && in_array($type, [Payments::TYPE_INCOMING, Payments::TYPE_OUTGOING])) {
            $query->andWhere(['type' => $type]);
        }

        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', $dateFrom . ' 00:00:00']);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $parties = Parties::find()->asArray()->all();
        $partyList = array_combine(
            array_column($parties, 'id'),
            array_column($parties, 'name')
        );

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'partyList' => $partyList,
            'partyId' => $partyId,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function actionCreate()
    {
        $model = new Payments();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if (empty($model->party_id) || empty($model->type) || !isset($model->amount) || $model->amount <= 0) {
                Yii::$app->session->setFlash('error', 'Please fill all required fields with valid values.');
                return $this->render('create', ['model' => $model]);
            }

            $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);

            try {
                if (!$model->save()) {
                    throw new \Exception('Failed to save payment: ' . json_encode($model->errors));
                }

                if ($model->reference_type === 'sale' && $model->reference_id) {
                    $this->updateSalePayment($model->reference_id, (float)$model->amount);
                } elseif ($model->reference_type === 'purchase' && $model->reference_id) {
                    $this->updatePurchasePayment($model->reference_id, (float)$model->amount);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Payment recorded successfully.');

                return $this->redirect(['index']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error recording payment: ' . $e->getMessage());
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionQuickAdd()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $saleId = Yii::$app->request->post('sale_id');
        $purchaseId = Yii::$app->request->post('purchase_id');
        $amount = Yii::$app->request->post('amount');
        $method = Yii::$app->request->post('method');

        if (!$amount || !$method) {
            return ['success' => false, 'message' => 'Amount and method are required.'];
        }

        $amount = (float)$amount;
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Amount must be greater than zero.'];
        }

        if ($saleId) {
            $sale = Sales::findOne($saleId);
            if (!$sale) {
                return ['success' => false, 'message' => 'Sale not found.'];
            }
            $partyId = $sale->customer_id;
            $type = Payments::TYPE_INCOMING;
            $referenceType = 'sale';
            $referenceId = $saleId;
        } elseif ($purchaseId) {
            $purchase = Purchases::findOne($purchaseId);
            if (!$purchase) {
                return ['success' => false, 'message' => 'Purchase not found.'];
            }
            $partyId = $purchase->supplier_id;
            $type = Payments::TYPE_OUTGOING;
            $referenceType = 'purchase';
            $referenceId = $purchaseId;
        } else {
            return ['success' => false, 'message' => 'Sale or Purchase ID is required.'];
        }

        $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);

        try {
            $payment = new Payments();
            $payment->party_id = $partyId;
            $payment->type = $type;
            $payment->amount = $amount;
            $payment->method = $method;
            $payment->reference_type = $referenceType;
            $payment->reference_id = $referenceId;
            $payment->created_at = date('Y-m-d H:i:s');

            if (!$payment->save()) {
                throw new \Exception('Failed to save payment: ' . json_encode($payment->errors));
            }
            if ($referenceType === 'sale') {
                $this->updateSalePayment($referenceId, $amount);
            } else {
                $this->updatePurchasePayment($referenceId, $amount);
            }

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'payment_id' => $payment->id,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    protected function updateSalePayment($saleId, $amount)
    {
        $sale = Sales::findOne($saleId);
        if (!$sale) {
            throw new \Exception('Sale not found.');
        }

        $sale->paid_amount = (float)($sale->paid_amount ?? 0) + $amount;

        if ($sale->paid_amount > $sale->total_amount) {
            $sale->paid_amount = $sale->total_amount;
        }

        if ($sale->paid_amount >= $sale->total_amount) {
            $sale->status = Sales::STATUS_PAID;
        } elseif ($sale->paid_amount > 0) {
            $sale->status = Sales::STATUS_PARTIAL;
        } else {
            $sale->status = Sales::STATUS_PENDING;
        }

        if (!$sale->save(false)) {
            throw new \Exception('Failed to update sale: ' . json_encode($sale->errors));
        }
    }

    protected function updatePurchasePayment($purchaseId, $amount)
    {
        $purchase = Purchases::findOne($purchaseId);
        if (!$purchase) {
            throw new \Exception('Purchase not found.');
        }

        $purchase->paid_amount = (float)($purchase->paid_amount ?? 0) + $amount;

        if ($purchase->paid_amount > $purchase->total_amount) {
            $purchase->paid_amount = $purchase->total_amount;
        }

        if ($purchase->paid_amount >= $purchase->total_amount) {
            $purchase->status = Purchases::STATUS_PAID;
        } elseif ($purchase->paid_amount > 0) {
            $purchase->status = Purchases::STATUS_PARTIAL;
        } else {
            $purchase->status = Purchases::STATUS_PENDING;
        }

        if (!$purchase->save(false)) {
            throw new \Exception('Failed to update purchase: ' . json_encode($purchase->errors));
        }
    }
}
