<?php

namespace app\controllers;

use app\models\Parties;
use app\models\Payments;
use app\models\Purchases;
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

        // Calculate ledger
        $totalSales = Sales::find()->where(['customer_id' => $id])->sum('total_amount') ?? 0;
        $totalPurchases = Purchases::find()->where(['supplier_id' => $id])->sum('total_amount') ?? 0;
        $totalIncomingPayments = Payments::find()->where(['party_id' => $id, 'type' => Payments::TYPE_INCOMING])->sum('amount') ?? 0;
        $totalOutgoingPayments = Payments::find()->where(['party_id' => $id, 'type' => Payments::TYPE_OUTGOING])->sum('amount') ?? 0;

        $customerBalance = $totalSales - $totalIncomingPayments; // They owe you
        $supplierBalance = $totalPurchases - $totalOutgoingPayments; // You owe them

        return $this->render('view', [
            'model' => $model,
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalIncomingPayments' => $totalIncomingPayments,
            'totalOutgoingPayments' => $totalOutgoingPayments,
            'customerBalance' => $customerBalance,
            'supplierBalance' => $supplierBalance,
        ]);
    }

    /**
     * Creates a new Parties model.
     * @return mixed
     */
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

    /**
     * Updates an existing Parties model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
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

    /**
     * Deletes an existing Parties model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Party deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Parties model based on its primary key value.
     * @param integer $id
     * @return Parties
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Parties::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested party does not exist.');
    }
}
