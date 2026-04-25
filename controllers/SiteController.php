<?php

namespace app\controllers;

use app\components\AppController;
use app\models\Users;
use Yii;
use yii\web\Response;
use app\models\LoginForm;
use app\models\Sales;
use app\models\Purchases;
use app\models\Payments;
use app\models\Products;
use app\models\InventoryTransactions;

class SiteController extends AppController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Allow guests on login, logout, error, and the one-time setup page
        $behaviors['access']['except'] = ['login', 'logout', 'error', 'setup'];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays dashboard with key summaries.
     *
     * @return string
     */
    public function actionIndex()
    {
        $totalSales = Sales::find()->sum('total_amount') ?? 0;
        $totalSalesPaid = Sales::find()->sum('paid_amount') ?? 0;
        $totalSalesOutstanding = ($totalSales - $totalSalesPaid);
        $salesCount = Sales::find()->count();
        $paidSalesCount = Sales::find()->where(['status' => Sales::STATUS_PAID])->count();
        $pendingSalesCount = Sales::find()->where(['status' => Sales::STATUS_PENDING])->count();

        $totalPurchases = Purchases::find()->sum('total_amount') ?? 0;
        $totalPurchasesPaid = Purchases::find()->sum('paid_amount') ?? 0;
        $totalPurchasesOutstanding = ($totalPurchases - $totalPurchasesPaid);
        $purchasesCount = Purchases::find()->count();
        $paidPurchasesCount = Purchases::find()->where(['status' => Purchases::STATUS_PAID])->count();
        $pendingPurchasesCount = Purchases::find()->where(['status' => Purchases::STATUS_PENDING])->count();

        $totalIncomingPayments = Payments::find()->where(['type' => Payments::TYPE_INCOMING])->sum('amount') ?? 0;
        $totalOutgoingPayments = Payments::find()->where(['type' => Payments::TYPE_OUTGOING])->sum('amount') ?? 0;
        $totalPayments = $totalIncomingPayments + $totalOutgoingPayments;
        $paymentsCount = Payments::find()->count();

        $productsCount = Products::find()->count();
        
        $recentPayments = Payments::find()->orderBy(['id' => SORT_DESC])->limit(5)->all();
        $recentSales = Sales::find()->orderBy(['id' => SORT_DESC])->limit(5)->all();
        $recentPurchases = Purchases::find()->orderBy(['id' => SORT_DESC])->limit(5)->all();

        return $this->render('dashboard', [
            'totalSales' => $totalSales,
            'totalSalesPaid' => $totalSalesPaid,
            'totalSalesOutstanding' => $totalSalesOutstanding,
            'salesCount' => $salesCount,
            'paidSalesCount' => $paidSalesCount,
            'pendingSalesCount' => $pendingSalesCount,
            
            'totalPurchases' => $totalPurchases,
            'totalPurchasesPaid' => $totalPurchasesPaid,
            'totalPurchasesOutstanding' => $totalPurchasesOutstanding,
            'purchasesCount' => $purchasesCount,
            'paidPurchasesCount' => $paidPurchasesCount,
            'pendingPurchasesCount' => $pendingPurchasesCount,
            
            'totalIncomingPayments' => $totalIncomingPayments,
            'totalOutgoingPayments' => $totalOutgoingPayments,
            'totalPayments' => $totalPayments,
            'paymentsCount' => $paymentsCount,
            
            'productsCount' => $productsCount,
            
            'recentPayments' => $recentPayments,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->user->identity->role === Users::ROLE_ADMIN) {
                return $this->redirect(['admin/index']);
            }
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    }
}
