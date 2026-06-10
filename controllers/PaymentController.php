<?php

namespace app\controllers;

use app\models\AccountTransactions;
use app\models\Payments;
use app\models\Parties;
use app\models\Sales;
use app\models\Purchases;
use Yii;
use app\components\AppController;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class PaymentController extends AppController
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

                // Write account transaction if an account was chosen
                if ($model->account_id) {
                    $this->writeAccountTransaction($model);
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

        $accountId = Yii::$app->request->post('account_id');

        $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);

        try {
            $payment = new Payments();
            $payment->party_id = $partyId;
            $payment->type = $type;
            $payment->amount = $amount;
            $payment->method = $method;
            $payment->account_id = $accountId ?: null;
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

            if ($payment->account_id) {
                $this->writeAccountTransaction($payment);
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

    public function actionDelete($id)
    {
        $payment = Payments::findOne($id);
        if (!$payment) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Payment not found.'];
            }
            throw new NotFoundHttpException('Payment not found.');
        }

        $referenceType = $payment->reference_type;
        $referenceId   = $payment->reference_id;
        $transaction   = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
        $isAjax = Yii::$app->request->isAjax;

        try {
            // 1. Delete linked account_transaction (if any)
            AccountTransactions::deleteAll(['reference_type' => 'payment', 'reference_id' => $id]);

            // 2. Delete the payment
            $payment->delete();

            // 3. Recompute paid_amount/status on parent from remaining payments
            if ($referenceType === 'sale' && $referenceId) {
                $sale = Sales::findOne($referenceId);
                if ($sale) {
                    $paid = (float)(Payments::find()
                        ->where(['reference_type' => 'sale', 'reference_id' => $referenceId])
                        ->sum('amount') ?? 0);
                    $paid = min($paid, (float)$sale->total_amount);
                    $sale->paid_amount = $paid;
                    $sale->status = $paid >= (float)$sale->total_amount
                        ? Sales::STATUS_PAID
                        : ($paid > 0 ? Sales::STATUS_PARTIAL : Sales::STATUS_PENDING);
                    $sale->save(false);
                }
            } elseif ($referenceType === 'purchase' && $referenceId) {
                $purchase = Purchases::findOne($referenceId);
                if ($purchase) {
                    $paid = (float)(Payments::find()
                        ->where(['reference_type' => 'purchase', 'reference_id' => $referenceId])
                        ->sum('amount') ?? 0);
                    $paid = min($paid, (float)$purchase->total_amount);
                    $purchase->paid_amount = $paid;
                    $purchase->status = $paid >= (float)$purchase->total_amount
                        ? Purchases::STATUS_PAID
                        : ($paid > 0 ? Purchases::STATUS_PARTIAL : Purchases::STATUS_PENDING);
                    $purchase->save(false);
                }
            }

            $transaction->commit();
            $msg = 'Payment #' . $id . ' deleted and balances recalculated.';

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true, 'message' => $msg];
            }
            Yii::$app->session->setFlash('success', $msg);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $msg = 'Failed to delete payment: ' . $e->getMessage();

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => $msg];
            }
            Yii::$app->session->setFlash('error', $msg);
        }

        $referer = Yii::$app->request->referrer;
        return $this->redirect($referer ?: ['index']);
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

    /**
     * Write an AccountTransaction for the given payment.
     * Incoming payments = credit (money comes in to account).
     * Outgoing payments = debit (money goes out of account).
     */
    protected function writeAccountTransaction(Payments $payment)
    {
        $at = new AccountTransactions();
        $at->account_id = $payment->account_id;
        $at->type = $payment->type === Payments::TYPE_INCOMING
            ? AccountTransactions::TYPE_CREDIT
            : AccountTransactions::TYPE_DEBIT;
        $at->amount = $payment->amount;
        $at->reference_type = 'payment';
        $at->reference_id = $payment->id;
        $at->description = ($payment->type === Payments::TYPE_INCOMING ? 'Payment from ' : 'Payment to ')
            . ($payment->party ? $payment->party->name : 'party #' . $payment->party_id);
        $at->created_at = $payment->created_at ?: date('Y-m-d H:i:s');

        if (!$at->save()) {
            throw new \Exception('Failed to record account transaction: ' . json_encode($at->errors));
        }
    }
}
