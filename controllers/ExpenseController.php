<?php

namespace app\controllers;

use app\models\AccountTransactions;
use app\models\Accounts;
use app\models\ChemicalAccount;
use app\models\ExpenseCategories;
use app\models\Expenses;
use app\models\NilOperations;
use Yii;
use app\components\AppController;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ExpenseController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-expense' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * All expense categories with their pending totals.
     */
    public function actionIndex()
    {
        $categories = ExpenseCategories::find()->orderBy(['name' => SORT_ASC])->all();
        $chemicalBalance = ChemicalAccount::calculateBalance();

        $categoryData = [];
        foreach ($categories as $cat) {
            $categoryData[] = [
                'id'            => $cat->id,
                'name'          => $cat->name,
                'pending_total' => Expenses::currentTotal($cat->id),
                'model'         => $cat,
            ];
        }

        return $this->render('index', [
            'categoryData'   => $categoryData,
            'chemicalBalance' => $chemicalBalance,
        ]);
    }

    /**
     * Add an expense — deducts from the chosen sub-account.
     */
    public function actionAdd()
    {
        $model = new Expenses();

        // Pre-select category from query param
        $model->category_id = Yii::$app->request->get('category_id');

        if ($model->load(Yii::$app->request->post())) {
            $dbTx = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            try {
                $model->created_at = date('Y-m-d H:i:s');

                if (!$model->save()) {
                    throw new \Exception(json_encode($model->errors));
                }

                // Debit the paying sub-account
                $at = new AccountTransactions();
                $at->account_id     = $model->paid_from_account_id;
                $at->type           = AccountTransactions::TYPE_DEBIT;
                $at->amount         = $model->amount;
                $at->reference_type = 'expense';
                $at->reference_id   = $model->id;
                $at->description    = ($model->category ? $model->category->name : 'Expense')
                    . ': ' . $model->description;
                $at->created_at     = $model->created_at;

                if (!$at->save()) {
                    throw new \Exception(json_encode($at->errors));
                }

                $dbTx->commit();
                Yii::$app->session->setFlash('success', 'Expense recorded successfully.');
                return $this->redirect(['view', 'id' => $model->category_id]);

            } catch (\Exception $e) {
                $dbTx->rollBack();
                Yii::$app->session->setFlash('error', 'Failed to save expense: ' . $e->getMessage());
            }
        }

        $categories   = ArrayHelper::map(ExpenseCategories::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
        $accounts     = Accounts::find()->orderBy(['name' => SORT_ASC])->all();
        $accountList  = [];
        foreach ($accounts as $acc) {
            $accountList[$acc->id] = $acc->name . ' (' . $acc->displayType() . ')';
        }

        return $this->render('add', [
            'model'      => $model,
            'categories' => $categories,
            'accounts'   => $accountList,
        ]);
    }

    /**
     * View all expenses for a specific category.
     */
    public function actionView($id)
    {
        $category = $this->findCategory($id);

        $dataProvider = new ActiveDataProvider([
            'query'      => Expenses::find()->where(['category_id' => $id])->with(['paidFromAccount']),
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $pendingTotal = Expenses::currentTotal($id);

        // Nil history for this category
        $nilDataProvider = new ActiveDataProvider([
            'query'      => NilOperations::find()->where(['category_id' => $id])->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('view', [
            'category'       => $category,
            'dataProvider'   => $dataProvider,
            'pendingTotal'   => $pendingTotal,
            'nilDataProvider' => $nilDataProvider,
        ]);
    }

    /**
     * Nil a specific expense category against Chemical Account.
     */
    public function actionNil($id)
    {
        $category       = $this->findCategory($id);
        $expenseTotal   = Expenses::currentTotal($id);
        $chemicalBefore = ChemicalAccount::calculateBalance();
        $chemicalAfter  = $chemicalBefore + $expenseTotal;

        if (Yii::$app->request->isPost) {
            if ($expenseTotal <= 0) {
                Yii::$app->session->setFlash('error', 'No pending expenses to settle for "' . $category->name . '".');
                return $this->redirect(['view', 'id' => $id]);
            }

            $dbTx = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            try {
                $nil = new NilOperations();
                $nil->category_id             = $id;
                $nil->chemical_balance_before = $chemicalBefore;
                $nil->expense_total           = $expenseTotal;
                $nil->chemical_balance_after  = $chemicalAfter;
                $nil->performed_by            = Yii::$app->user->id;
                $nil->notes                   = Yii::$app->request->post('notes');
                $nil->created_at              = date('Y-m-d H:i:s');

                if (!$nil->save()) {
                    throw new \Exception(json_encode($nil->errors));
                }

                $dbTx->commit();
                Yii::$app->session->setFlash('success',
                    '"' . $category->name . '" nilled. Chemical Account increased by '
                    . number_format($expenseTotal) . '.'
                );
                return $this->redirect(['view', 'id' => $id]);

            } catch (\Exception $e) {
                $dbTx->rollBack();
                Yii::$app->session->setFlash('error', 'Nil operation failed: ' . $e->getMessage());
            }
        }

        return $this->render('nil', [
            'category'      => $category,
            'expenseTotal'  => $expenseTotal,
            'chemicalBefore' => $chemicalBefore,
            'chemicalAfter' => $chemicalAfter,
        ]);
    }

    /**
     * Create a new expense category.
     */
    public function actionCreateCategory()
    {
        $model = new ExpenseCategories();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Category "' . $model->name . '" created.');
            return $this->redirect(['index']);
        }

        return $this->render('create-category', ['model' => $model]);
    }

    protected function findCategory(int $id): ExpenseCategories
    {
        $model = ExpenseCategories::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Expense category not found.');
        }
        return $model;
    }

    public function actionDeleteExpense($id)
    {
        $expense = Expenses::findOne($id);
        if (!$expense) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Expense not found.'];
            }
            throw new NotFoundHttpException('Expense not found.');
        }

        $categoryId = $expense->category_id;
        $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
        $isAjax = Yii::$app->request->isAjax;

        try {
            // 1. Delete the account_transaction that debited the sub-account
            AccountTransactions::deleteAll(['reference_type' => 'expense', 'reference_id' => $id]);

            // 2. Delete the expense row
            $expense->delete();

            $transaction->commit();
            $msg = 'Expense deleted and account balance restored.';

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true, 'message' => $msg];
            }
            Yii::$app->session->setFlash('success', $msg);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $msg = 'Failed to delete expense: ' . $e->getMessage();

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => $msg];
            }
            Yii::$app->session->setFlash('error', $msg);
        }

        return $this->redirect(['view', 'id' => $categoryId]);
    }
}
