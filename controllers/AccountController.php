<?php

namespace app\controllers;

use app\models\AccountTransactions;
use app\models\AccountTransfers;
use app\models\Accounts;
use app\models\ChemicalAccount;
use app\models\NilOperations;
use app\models\Purchases;
use app\models\Sales;
use Yii;
use app\components\AppController;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class AccountController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-transaction' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Chemical Account overview + sub-account list with balances.
     */
    public function actionIndex()
    {
        $chemicalBalance = ChemicalAccount::calculateBalance();
        $accounts = Accounts::find()->orderBy(['name' => SORT_ASC])->all();

        $accountData = [];
        foreach ($accounts as $account) {
            $accountData[] = [
                'id'      => $account->id,
                'name'    => $account->name,
                'type'    => $account->displayType(),
                'balance' => $account->getBalance(),
                'model'   => $account,
            ];
        }

        return $this->render('index', [
            'chemicalBalance' => $chemicalBalance,
            'accountData'     => $accountData,
        ]);
    }

    /**
     * Create a new sub-account.
     */
    public function actionCreate()
    {
        $model = new Accounts();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Account "' . $model->name . '" created.');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * View a single sub-account with its transaction ledger.
     */
    public function actionView($id)
    {
        $account = $this->findAccount($id);

        $dataProvider = new ActiveDataProvider([
            'query'      => AccountTransactions::find()->where(['account_id' => $id]),
            'pagination' => ['pageSize' => 50],
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        return $this->render('view', [
            'account'      => $account,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Transfer funds between two sub-accounts.
     * Creates debit on from_account and credit on to_account.
     * Chemical Account balance is NOT affected.
     */
    public function actionTransfer()
    {
        $model = new AccountTransfers();

        if ($model->load(Yii::$app->request->post())) {
            $dbTx = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            try {
                $model->created_at = date('Y-m-d H:i:s');
                if (!$model->save()) {
                    throw new \Exception('Failed to save transfer: ' . json_encode($model->errors));
                }

                // Debit from source account
                $debit = new AccountTransactions();
                $debit->account_id    = $model->from_account_id;
                $debit->type          = AccountTransactions::TYPE_DEBIT;
                $debit->amount        = $model->amount;
                $debit->reference_type = 'transfer';
                $debit->reference_id  = $model->id;
                $debit->description   = 'Transfer to ' . $model->toAccount->name
                    . ($model->description ? ': ' . $model->description : '');
                $debit->created_at    = $model->created_at;
                if (!$debit->save()) {
                    throw new \Exception('Debit tx failed: ' . json_encode($debit->errors));
                }

                // Credit destination account
                $credit = new AccountTransactions();
                $credit->account_id    = $model->to_account_id;
                $credit->type          = AccountTransactions::TYPE_CREDIT;
                $credit->amount        = $model->amount;
                $credit->reference_type = 'transfer';
                $credit->reference_id  = $model->id;
                $credit->description   = 'Transfer from ' . $model->fromAccount->name
                    . ($model->description ? ': ' . $model->description : '');
                $credit->created_at    = $model->created_at;
                if (!$credit->save()) {
                    throw new \Exception('Credit tx failed: ' . json_encode($credit->errors));
                }

                $dbTx->commit();
                Yii::$app->session->setFlash('success', 'Transfer of ' . number_format($model->amount) . ' completed.');
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                $dbTx->rollBack();
                Yii::$app->session->setFlash('error', 'Transfer failed: ' . $e->getMessage());
            }
        }

        $accounts = Accounts::find()->orderBy(['name' => SORT_ASC])->all();
        $accountList = \yii\helpers\ArrayHelper::map($accounts, 'id', 'name');

        return $this->render('transfer', [
            'model'       => $model,
            'accountList' => $accountList,
        ]);
    }

    /**
     * Delete a single account transaction with smart cascade logic:
     * - transfer: deletes both sides + the account_transfers row
     * - payment / expense: refused — delete the parent record instead
     * - anything else (manual, etc.): direct delete
     */
    public function actionDeleteTransaction($id)
    {
        $tx = AccountTransactions::findOne($id);
        $isAjax = Yii::$app->request->isAjax;

        if (!$tx) {
            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Transaction not found.'];
            }
            throw new NotFoundHttpException('Transaction not found.');
        }

        $accountId = $tx->account_id;

        if (in_array($tx->reference_type, ['payment', 'expense'], true)) {
            $label = $tx->reference_type === 'payment' ? 'payment' : 'expense';
            $msg   = 'This entry is linked to a ' . $label . '. Delete the ' . $label . ' record itself to revert it properly.';
            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => $msg];
            }
            Yii::$app->session->setFlash('error', $msg);
            return $this->redirect(['view', 'id' => $accountId]);
        }

        $dbTx = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);

        try {
            if ($tx->reference_type === 'transfer' && $tx->reference_id) {
                // Delete both sides of the transfer and the transfer record
                AccountTransactions::deleteAll([
                    'reference_type' => 'transfer',
                    'reference_id'   => $tx->reference_id,
                ]);
                AccountTransfers::deleteAll(['id' => $tx->reference_id]);
            } else {
                $tx->delete();
            }

            $dbTx->commit();
            $msg = 'Transaction deleted.';

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true, 'message' => $msg];
            }
            Yii::$app->session->setFlash('success', $msg);
        } catch (\Exception $e) {
            $dbTx->rollBack();
            $msg = 'Failed to delete transaction: ' . $e->getMessage();

            if ($isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'message' => $msg];
            }
            Yii::$app->session->setFlash('error', $msg);
        }

        return $this->redirect(['view', 'id' => $accountId]);
    }

    /**
     * Chemical Account configuration (set opening balance).
     */
    public function actionChemical()
    {
        $model = ChemicalAccount::getConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Chemical Account opening balance updated.');
            return $this->redirect(['index']);
        }

        $balance = ChemicalAccount::calculateBalance();

        return $this->render('chemical', [
            'model'   => $model,
            'balance' => $balance,
        ]);
    }

    /**
     * Chemical Account ledger — all purchases (credits), sales (debits), nil operations (debits)
     * sorted chronologically with a running balance column.
     */
    public function actionChemicalLedger()
    {
        $config = ChemicalAccount::getConfig();
        $openingBalance = (float)$config->opening_balance;

        $rows = [];

        // Purchases → credit (increase chemical value)
        $purchases = Purchases::find()
            ->with(['supplier', 'purchaseItems.product', 'purchaseItems.productUnit'])
            ->orderBy(['purchase_date' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        foreach ($purchases as $p) {
            $rows[] = [
                'date'       => $p->purchase_date,
                'type'       => 'purchase',
                'party_id'   => $p->supplier_id,
                'party_name' => $p->supplier ? $p->supplier->name : null,
                'items_desc' => $this->buildLedgerItemsDesc($p->purchaseItems),
                'credit'     => (float)$p->total_amount,
                'debit'      => 0.0,
            ];
        }

        // Sales → debit (decrease chemical value)
        $sales = Sales::find()
            ->with(['customer', 'saleItems.product', 'saleItems.productUnit'])
            ->orderBy(['sale_date' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        foreach ($sales as $s) {
            $rows[] = [
                'date'       => $s->sale_date,
                'type'       => 'sale',
                'party_id'   => $s->customer_id,
                'party_name' => $s->customer ? $s->customer->name : null,
                'items_desc' => $this->buildLedgerItemsDesc($s->saleItems),
                'credit'     => 0.0,
                'debit'      => (float)$s->total_amount,
            ];
        }

        // Nil operations → debit (expense settlement)
        $nils = NilOperations::find()
            ->with('category')
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        foreach ($nils as $n) {
            $catName = $n->category ? $n->category->name : 'Expense';
            $rows[] = [
                'date'       => $n->created_at,
                'type'       => 'nil',
                'party_id'   => null,
                'party_name' => $catName,
                'items_desc' => $n->notes ?: null,
                'credit'     => 0.0,
                'debit'      => (float)$n->expense_total,
            ];
        }

        // Sort all rows by date ascending, then by type order (purchase < nil < sale) for same-date
        usort($rows, function ($a, $b) {
            $cmp = strcmp($a['date'], $b['date']);
            return $cmp !== 0 ? $cmp : strcmp($a['type'], $b['type']);
        });

        // Compute running balance
        $runningBalance = $openingBalance;
        foreach ($rows as &$row) {
            $runningBalance += $row['credit'] - $row['debit'];
            $row['balance'] = $runningBalance;
        }
        unset($row);

        return $this->render('chemical-ledger', [
            'rows'           => $rows,
            'openingBalance' => $openingBalance,
            'currentBalance' => $runningBalance,
        ]);
    }

    /**
     * Build "Product - Unit × Qty, ..." string from an array of SaleItems or PurchaseItems.
     */
    private function buildLedgerItemsDesc(array $items): string
    {
        $parts = [];
        foreach ($items as $item) {
            $name = $item->product ? $item->product->name : '?';
            $unit = $item->productUnit ? $item->productUnit->unit_name : '';
            $qty  = rtrim(rtrim(number_format((float)$item->quantity, 3, '.', ''), '0'), '.');
            $parts[] = $unit ? "{$name} - {$unit} × {$qty}" : "{$name} × {$qty}";
        }
        return implode(', ', $parts);
    }

    protected function findAccount($id): Accounts
    {
        $model = Accounts::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Account not found.');
        }
        return $model;
    }
}
