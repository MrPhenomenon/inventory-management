<?php

namespace app\controllers;

use app\components\AppController;
use app\models\Accounts;
use app\models\ChemicalAccount;
use app\models\ExpenseCategories;
use app\models\Expenses;
use app\models\Parties;
use app\models\Payments;
use app\models\Products;
use app\models\Purchases;
use app\models\Sales;
use app\models\Users;
use Yii;
use yii\web\Response;
use app\models\LoginForm;

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
        // ── Chemical Account ─────────────────────────────────────────────────
        $chemicalBalance = ChemicalAccount::calculateBalance();

        // ── Sub-accounts ─────────────────────────────────────────────────────
        $accounts = Accounts::find()->orderBy(['name' => SORT_ASC])->all();
        $accountRows = [];
        $totalAccountsBalance = 0;
        foreach ($accounts as $acc) {
            $bal = $acc->getBalance();
            $accountRows[] = ['name' => $acc->name, 'balance' => $bal];
            $totalAccountsBalance += $bal;
        }

        // ── Receivables (customers with positive balance) ─────────────────────
        $totalReceivables = 0;
        $receivableCount  = 0;
        $customers = Parties::find()
            ->where(['in', 'type', [Parties::TYPE_CUSTOMER, Parties::TYPE_BOTH]])
            ->all();
        foreach ($customers as $c) {
            $sales    = (float)(Sales::find()->where(['customer_id' => $c->id])->sum('total_amount') ?? 0);
            $paid     = (float)(Payments::find()->where(['party_id' => $c->id, 'type' => Payments::TYPE_INCOMING])->sum('amount') ?? 0);
            $opening  = $c->opening_balance_type === 'receivable' ? (float)$c->opening_balance : 0;
            $bal      = $sales - $paid + $opening;
            if ($bal > 0) {
                $totalReceivables += $bal;
                $receivableCount++;
            }
        }

        // ── Payables (suppliers with positive balance) ────────────────────────
        $totalPayables = 0;
        $payableCount  = 0;
        $suppliers = Parties::find()
            ->where(['in', 'type', [Parties::TYPE_SUPPLIER, Parties::TYPE_BOTH]])
            ->all();
        foreach ($suppliers as $s) {
            $purchases = (float)(Purchases::find()->where(['supplier_id' => $s->id])->sum('total_amount') ?? 0);
            $paid      = (float)(Payments::find()->where(['party_id' => $s->id, 'type' => Payments::TYPE_OUTGOING])->sum('amount') ?? 0);
            $opening   = $s->opening_balance_type === 'payable' ? (float)$s->opening_balance : 0;
            $bal       = $purchases - $paid + $opening;
            if ($bal > 0) {
                $totalPayables += $bal;
                $payableCount++;
            }
        }

        // ── Pending expenses ──────────────────────────────────────────────────
        $expenseRows = [];
        $totalPendingExpenses = 0;
        foreach (ExpenseCategories::find()->orderBy(['name' => SORT_ASC])->all() as $cat) {
            $pending = Expenses::currentTotal($cat->id);
            if ($pending > 0) {
                $expenseRows[] = ['id' => $cat->id, 'name' => $cat->name, 'pending' => $pending];
                $totalPendingExpenses += $pending;
            }
        }

        // ── Recent activity ───────────────────────────────────────────────────
        $recentSales = Sales::find()
            ->with(['customer', 'saleItems.product', 'saleItems.productUnit'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(6)
            ->all();

        $recentPurchases = Purchases::find()
            ->with(['supplier', 'purchaseItems.product', 'purchaseItems.productUnit'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(6)
            ->all();

        return $this->render('dashboard', [
            'chemicalBalance'      => $chemicalBalance,
            'accountRows'          => $accountRows,
            'totalAccountsBalance' => $totalAccountsBalance,
            'totalReceivables'     => $totalReceivables,
            'receivableCount'      => $receivableCount,
            'totalPayables'        => $totalPayables,
            'payableCount'         => $payableCount,
            'expenseRows'          => $expenseRows,
            'totalPendingExpenses' => $totalPendingExpenses,
            'recentSales'          => $recentSales,
            'recentPurchases'      => $recentPurchases,
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
        return $this->redirect(['site/login']);
    }
}
