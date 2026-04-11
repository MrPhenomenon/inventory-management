<?php

namespace app\controllers;

use app\models\InventoryTransactions;
use app\models\Products;
use app\models\ProductUnits;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class InventoryController extends Controller
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
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'attributes' => ['name', 'category', 'base_stock'],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTransactions()
    {
        $query = InventoryTransactions::find();

        $productId = Yii::$app->request->get('product_id');
        if ($productId) {
            $query->where(['product_id' => $productId]);
        }

        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', $dateFrom . ' 00:00:00']);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59']);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $products = Products::find()->asArray()->all();
        $productList = array_combine(
            array_column($products, 'id'),
            array_column($products, 'name')
        );

        return $this->render('transactions', [
            'dataProvider' => $dataProvider,
            'productList' => $productList,
            'productId' => $productId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function actionAdjust()
    {
        $model = new InventoryTransactions();
        $model->type = InventoryTransactions::TYPE_ADJUSTMENT;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->load($post);

            if (empty($model->product_id) || empty($model->product_unit_id) || !isset($model->quantity)) {
                Yii::$app->session->setFlash('error', 'Please fill all required fields.');
                return $this->render('adjust', ['model' => $model]);
            }

            $productUnit = ProductUnits::findOne($model->product_unit_id);
            if (!$productUnit || $productUnit->product_id != $model->product_id) {
                Yii::$app->session->setFlash('error', 'Invalid product or unit selection.');
                return $this->render('adjust', ['model' => $model]);
            }

            $model->base_quantity = (float)$model->quantity * (float)$productUnit->conversion_to_base;
            $model->reference_type = 'manual_adjustment';
            $model->reference_id = null;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Stock adjusted successfully.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to save adjustment: ' . json_encode($model->errors));
            }
        }

        return $this->render('adjust', ['model' => $model]);
    }

    protected function calculateBaseStock($productId)
    {
        $sql = "SELECT COALESCE(SUM(CASE 
                    WHEN type = :typeIn THEN base_quantity
                    WHEN type = :typeOut THEN -base_quantity
                    WHEN type = :typeAdjustment THEN base_quantity
                    ELSE 0
                END), 0) AS stock FROM inventory_transactions WHERE product_id = :product_id";

        $stock = Yii::$app->db->createCommand($sql, [
            ':typeIn' => InventoryTransactions::TYPE_IN,
            ':typeOut' => InventoryTransactions::TYPE_OUT,
            ':typeAdjustment' => InventoryTransactions::TYPE_ADJUSTMENT,
            ':product_id' => $productId,
        ])->queryScalar();

        return (float)$stock;
    }
}
