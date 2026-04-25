<?php

namespace app\controllers;

use app\models\ProductUnits;
use app\models\Products;
use app\components\AppController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class ProductunitController extends AppController
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

    /**
     * Lists all ProductUnits for a specific product.
     * @param integer $product_id
     * @return mixed
     */
    public function actionIndex($product_id)
    {
        $product = $this->findProduct($product_id);

        $dataProvider = new ActiveDataProvider([
            'query' => ProductUnits::find()->where(['product_id' => $product_id]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'product' => $product,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ProductUnits model for a specific product.
     * @param integer $product_id
     * @return mixed
     */
    public function actionCreate($product_id)
    {
        $product = $this->findProduct($product_id);
        $model = new ProductUnits();
        $model->product_id = $product_id;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Product unit created successfully.');
            return $this->redirect(['index', 'product_id' => $product_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'product' => $product,
        ]);
    }

    /**
     * Updates an existing ProductUnits model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $product = $model->product;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Product unit updated successfully.');
            return $this->redirect(['index', 'product_id' => $model->product_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'product' => $product,
        ]);
    }

    /**
     * Deletes an existing ProductUnits model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $product_id = $model->product_id;
        $model->delete();
        \Yii::$app->session->setFlash('success', 'Product unit deleted successfully.');
        return $this->redirect(['index', 'product_id' => $product_id]);
    }

    /**
     * AJAX endpoint: Returns JSON list of units for a product.
     * @param integer $product_id
     * @return array
     */
    public function actionList($product_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $units = ProductUnits::find()
            ->where(['product_id' => $product_id])
            ->asArray()
            ->all();

        return [
            'success' => true,
            'data' => $units,
        ];
    }

    /**
     * Finds the ProductUnits model based on its primary key value.
     * @param integer $id
     * @return ProductUnits the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductUnits::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product unit does not exist.');
    }

    /**
     * Finds the Products model based on its primary key value.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProduct($id)
    {
        if (($model = Products::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product does not exist.');
    }
}
