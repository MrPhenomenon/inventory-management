<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Sales;
use app\models\Purchases;
use app\models\Payments;
use app\models\Products;
use app\models\InventoryTransactions;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => AccessControl::class,
    //             'except' => ['login', 'error', 'captcha'],
    //             'rules' => [
    //                 [
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                 ],
    //             ],
    //         ],
    //         'verbs' => [
    //             'class' => VerbFilter::class,
    //             'actions' => [
    //                 'logout' => ['post'],
    //             ],
    //         ],
    //     ];
    // }

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

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
