<?php

namespace app\controllers;

use app\components\SalesService;
use app\models\Parties;
use app\models\ProductUnits;
use app\models\Products;
use app\models\SaleItems;
use app\models\Sales;
use app\models\InventoryTransactions;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SalesController extends Controller
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
     * Lists all Sales.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Sales::find(),
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sales model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Sales model with items and inventory deductions.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sales();
        $items = [];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->load($post);
            $model->sale_date = $model->sale_date ?: date('Y-m-d H:i:s');
            $items = $post['items'] ?? [];
            $validItems = [];

            if (empty($items)) {
                Yii::$app->session->setFlash('error', 'Please add at least one sale item.');
                return $this->render('create', ['model' => $model, 'items' => $items]);
            }

            foreach ($items as $itemData) {
                if (empty($itemData['product_id']) || empty($itemData['product_unit_id']) ||
                    !isset($itemData['quantity']) || !isset($itemData['price'])) {
                    continue;
                }

                $productUnit = ProductUnits::findOne($itemData['product_unit_id']);
                if (!$productUnit) {
                    Yii::$app->session->setFlash('error', 'One of the selected product units is invalid.');
                    return $this->render('create', ['model' => $model, 'items' => $items]);
                }

                $quantity = (float) $itemData['quantity'];
                $price = (float) $itemData['price'];
                if ($quantity <= 0 || $price < 0) {
                    Yii::$app->session->setFlash('error', 'Quantity must be greater than zero and price must be non-negative.');
                    return $this->render('create', ['model' => $model, 'items' => $items]);
                }

                $baseQuantity = $quantity * $productUnit->conversion_to_base;
                $validItems[] = [
                    'product_id' => (int) $itemData['product_id'],
                    'product_unit_id' => (int) $itemData['product_unit_id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price,
                    'base_quantity' => $baseQuantity,
                ];
            }

            if (empty($validItems)) {
                Yii::$app->session->setFlash('error', 'Please add at least one valid sale item.');
                return $this->render('create', ['model' => $model, 'items' => $items]);
            }

            try {
                SalesService::validateStockAvailability($validItems);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create', ['model' => $model, 'items' => $items]);
            }

            $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);

            try {
                $model->total_amount = 0;
                if (!$model->save(false)) {
                    throw new \Exception('Failed to save sale.');
                }

                $saleId = $model->id;
                $totalAmount = 0;
                $savedItems = 0;

                foreach ($validItems as $itemData) {
                    $saleItem = new SaleItems();
                    $saleItem->sale_id = $saleId;
                    $saleItem->product_id = $itemData['product_id'];
                    $saleItem->product_unit_id = $itemData['product_unit_id'];
                    $saleItem->quantity = $itemData['quantity'];
                    $saleItem->price = $itemData['price'];
                    $saleItem->total = $itemData['total'];

                    if (!$saleItem->save()) {
                        throw new \Exception('Failed to save sale item: ' . json_encode($saleItem->errors));
                    }

                    $inventoryTransaction = new InventoryTransactions();
                    $inventoryTransaction->product_id = $itemData['product_id'];
                    $inventoryTransaction->type = InventoryTransactions::TYPE_OUT;
                    $inventoryTransaction->quantity = $itemData['quantity'];
                    $inventoryTransaction->product_unit_id = $itemData['product_unit_id'];
                    $inventoryTransaction->base_quantity = $itemData['base_quantity'];
                    $inventoryTransaction->reference_type = 'sale';
                    $inventoryTransaction->reference_id = $saleId;

                    if (!$inventoryTransaction->save()) {
                        throw new \Exception('Failed to save inventory transaction: ' . json_encode($inventoryTransaction->errors));
                    }

                    $totalAmount += $itemData['total'];
                    $savedItems++;
                }

                if ($savedItems === 0) {
                    throw new \Exception('No sale items were processed.');
                }

                $model->total_amount = $totalAmount;
                $paidAmount = (float) ($model->paid_amount ?? 0);
                if ($paidAmount >= $totalAmount) {
                    $model->status = Sales::STATUS_PAID;
                } elseif ($paidAmount > 0) {
                    $model->status = Sales::STATUS_PARTIAL;
                } else {
                    $model->status = Sales::STATUS_PENDING;
                }

                if (!$model->save(false)) {
                    throw new \Exception('Failed to update sale totals: ' . json_encode($model->errors));
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Sale created successfully.');

                return $this->redirect(['view', 'id' => $saleId]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error creating sale: ' . $e->getMessage());
                return $this->render('create', ['model' => $model, 'items' => $items]);
            }
        }

        return $this->render('create', ['model' => $model, 'items' => $items]);
    }

    /**
     * Finds the Sales model based on its primary key value.
     * @param integer $id
     * @return Sales
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Sales::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested sale does not exist.');
    }
}
