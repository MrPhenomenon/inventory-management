<?php

namespace app\controllers;

use app\models\AccountTransactions;
use app\models\InventoryTransactions;
use app\models\Parties;
use app\models\Payments;
use app\models\Products;
use app\models\ProductUnits;
use app\models\PurchaseItems;
use app\models\Purchases;
use Yii;
use app\components\AppController;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class PurchaseController extends AppController
{
    /**
     * {@inheritdoc}
     */
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
        $dataProvider = new ActiveDataProvider([
            'query' => Purchases::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $payments = Payments::find()->where(['reference_type' => 'purchase', 'reference_id' => $id])->all();
        $remainingBalance = (float)$model->total_amount - (float)$model->paid_amount;
        
        return $this->render('view', [
            'model' => $model,
            'payments' => $payments,
            'remainingBalance' => $remainingBalance,
        ]);
    }

    public function actionAddPayment($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['payment/create', 'purchase_id' => $id]);
    }

    public function actionCreate()
    {
        $model = new Purchases();
        $items = [];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            
            // Validate basic purchase data
            $model->load($post);
            $model->purchase_date = $model->purchase_date ?: date('Y-m-d H:i:s');
            
            // Get items from post
            $items_data = $post['items'] ?? [];
            
            // Validate at least one item
            if (empty($items_data)) {
                Yii::$app->session->setFlash('error', 'Please add at least one purchase item.');
                return $this->render('create', [
                    'model' => $model,
                    'items' => $items,
                ]);
            }

            // Start transaction
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Save purchase
                if (!$model->save(false)) {
                    throw new \Exception('Failed to save purchase: ' . json_encode($model->errors));
                }

                $purchase_id = $model->id;
                $total_amount = 0;
                $purchase_items_saved = 0;

                // Process each item
                foreach ($items_data as $item_data) {
                    if (empty($item_data['product_id']) || empty($item_data['product_unit_id']) || 
                        empty($item_data['quantity']) || !isset($item_data['price'])) {
                        continue;
                    }

                    // Get product unit for conversion
                    $productUnit = ProductUnits::findOne($item_data['product_unit_id']);
                    if (!$productUnit) {
                        throw new \Exception('Product unit not found.');
                    }

                    $quantity = (float) $item_data['quantity'];
                    $price = (float) $item_data['price'];
                    $item_total = $quantity * $price;

                    // Calculate base quantity
                    $base_quantity = $quantity * $productUnit->conversion_to_base;

                    // Create purchase item
                    $purchase_item = new PurchaseItems();
                    $purchase_item->purchase_id = $purchase_id;
                    $purchase_item->product_id = (int) $item_data['product_id'];
                    $purchase_item->product_unit_id = (int) $item_data['product_unit_id'];
                    $purchase_item->quantity = $quantity;
                    $purchase_item->price = $price;
                    $purchase_item->total = $item_total;

                    if (!$purchase_item->save()) {
                        throw new \Exception('Failed to save purchase item: ' . json_encode($purchase_item->errors));
                    }

                    // Create inventory transaction (IN)
                    $inventory_transaction = new InventoryTransactions();
                    $inventory_transaction->product_id = (int) $item_data['product_id'];
                    $inventory_transaction->type = InventoryTransactions::TYPE_IN;
                    $inventory_transaction->quantity = $quantity;
                    $inventory_transaction->product_unit_id = (int) $item_data['product_unit_id'];
                    $inventory_transaction->base_quantity = $base_quantity;
                    $inventory_transaction->reference_type = 'purchase';
                    $inventory_transaction->reference_id = $purchase_id;

                    if (!$inventory_transaction->save()) {
                        throw new \Exception('Failed to save inventory transaction: ' . json_encode($inventory_transaction->errors));
                    }

                    $total_amount += $item_total;
                    $purchase_items_saved++;
                }

                if ($purchase_items_saved === 0) {
                    throw new \Exception('No valid items were processed.');
                }

                // Update purchase with total amount
                $model->total_amount = $total_amount;

                // Update status based on payment
                $paid_amount = (float) ($model->paid_amount ?? 0);
                
                // Check if payment should be recorded
                $paymentMethod = $post['payment_method'] ?? '';
                
                if ($paid_amount > 0 && !empty($paymentMethod)) {
                    // Create payment record
                    $payment = new Payments();
                    $payment->party_id = $model->supplier_id;
                    $payment->type = Payments::TYPE_OUTGOING;
                    $payment->amount = $paid_amount;
                    $payment->method = $paymentMethod;
                    $payment->reference_type = 'purchase';
                    $payment->reference_id = $purchase_id;
                    $payment->created_at = date('Y-m-d H:i:s');
                    
                    if (!$payment->save()) {
                        throw new \Exception('Failed to save payment: ' . json_encode($payment->errors));
                    }
                }
                
                if ($paid_amount >= $total_amount) {
                    $model->status = Purchases::STATUS_PAID;
                } elseif ($paid_amount > 0) {
                    $model->status = Purchases::STATUS_PARTIAL;
                } else {
                    $model->status = Purchases::STATUS_PENDING;
                }
                
                $model->paid_amount = $paid_amount;

                if (!$model->save()) {
                    throw new \Exception('Failed to update purchase with total: ' . json_encode($model->errors));
                }

                // Commit transaction
                $transaction->commit();

                Yii::$app->session->setFlash('success', 'Purchase created successfully with ' . $purchase_items_saved . ' items.');
                return $this->redirect(['view', 'id' => $purchase_id]);

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error creating purchase: ' . $e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                    'items' => $items,
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'items' => $items,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
        $isAjax = Yii::$app->request->isAjax;

        try {
            // 1. Delete account_transactions linked to any payments for this purchase
            $paymentIds = Payments::find()
                ->where(['reference_type' => 'purchase', 'reference_id' => $id])
                ->select('id')
                ->column();

            if (!empty($paymentIds)) {
                AccountTransactions::deleteAll(['reference_type' => 'payment', 'reference_id' => $paymentIds]);
            }

            // 2. Delete payments for this purchase
            Payments::deleteAll(['reference_type' => 'purchase', 'reference_id' => $id]);

            // 3. Revert inventory (delete IN transactions)
            InventoryTransactions::deleteAll(['reference_type' => 'purchase', 'reference_id' => $id]);

            // 4. Delete purchase items
            PurchaseItems::deleteAll(['purchase_id' => $id]);

            // 5. Delete the purchase itself
            $model->delete();

            $transaction->commit();
            $msg = 'Purchase #' . $id . ' deleted and inventory reverted.';

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true, 'message' => $msg];
            }
            Yii::$app->session->setFlash('success', $msg);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $msg = 'Failed to delete purchase: ' . $e->getMessage();

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => $msg];
            }
            Yii::$app->session->setFlash('error', $msg);
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Purchases::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested purchase does not exist.');
    }
}
