<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Accounts $account */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = $account->name;
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$balance = $account->getBalance();
$balanceColor = $balance >= 0 ? 'success' : 'danger';
?>
<div class="account-view">

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-<?= $balanceColor ?> card-outline">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <p class="mb-1 text-muted">Account</p>
                            <h5><?= Html::encode($account->name) ?></h5>
                            <span class="badge badge-secondary"><?= $account->displayType() ?></span>
                        </div>
                        <div class="col text-right">
                            <p class="mb-1 text-muted">Balance</p>
                            <h3 class="text-<?= $balanceColor ?>"><?= number_format($balance) ?></h3>
                        </div>
                    </div>
                    <?php if ($account->opening_balance > 0): ?>
                    <small class="text-muted">Opening balance: <?= number_format($account->opening_balance) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Transaction Ledger</h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-exchange-alt"></i> Transfer', ['transfer'], ['class' => 'btn btn-sm btn-warning']) ?>
            </div>
        </div>
        <div class="card-body p-0">
            <?php
            // Pre-compute running balance for all rows (oldest first)
            $allTx = \app\models\AccountTransactions::find()
                ->where(['account_id' => $account->id])
                ->orderBy(['id' => SORT_ASC])
                ->all();
            $runningMap = [];
            $running = (float)$account->opening_balance;
            foreach ($allTx as $tx) {
                $running += $tx->type === 'credit' ? (float)$tx->amount : -(float)$tx->amount;
                $runningMap[$tx->id] = $running;
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'columns' => [
                    [
                        'attribute' => 'created_at',
                        'label' => 'Date',
                        'value' => function ($row) {
                            return date('d M Y', strtotime($row->created_at));
                        },
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function ($row) {
                            $color = $row->type === 'credit' ? 'success' : 'danger';
                            return '<span class="badge badge-' . $color . '">' . strtoupper($row->type) . '</span>';
                        },
                        'format' => 'html',
                    ],
                    'description',
                    [
                        'attribute' => 'amount',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'value' => function ($row) {
                            $sign = $row->type === 'credit' ? '+' : '-';
                            $color = $row->type === 'credit' ? 'text-success' : 'text-danger';
                            return '<span class="' . $color . '">' . $sign . number_format($row->amount) . '</span>';
                        },
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Balance',
                        'contentOptions' => ['class' => 'text-right font-weight-bold'],
                        'headerOptions' => ['class' => 'text-right'],
                        'format' => 'html',
                        'value' => function ($row) use ($runningMap) {
                            $bal = $runningMap[$row->id] ?? null;
                            if ($bal === null) return '—';
                            $color = $bal >= 0 ? 'text-dark' : 'text-danger';
                            return '<span class="' . $color . '">' . number_format($bal) . '</span>';
                        },
                    ],
                    [
                        'label'          => '',
                        'format'         => 'raw',
                        'contentOptions' => ['class' => 'text-right'],
                        'value'          => function ($row) {
                            $url   = \yii\helpers\Url::to(['/account/delete-transaction', 'id' => $row->id]);
                            $typeHint = match($row->reference_type) {
                                'transfer' => '[Transfer] ',
                                'payment'  => '[Payment] ',
                                'expense'  => '[Expense] ',
                                default    => '',
                            };
                            $label = $typeHint
                                . ($row->description ?: 'Transaction #' . $row->id)
                                . ' — ' . number_format($row->amount);
                            return '<button type="button" class="btn btn-sm btn-outline-danger"'
                                . ' data-delete-url="' . $url . '"'
                                . ' data-delete-label="' . \yii\helpers\Html::encode($label) . '">Delete</button>';
                        },
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
