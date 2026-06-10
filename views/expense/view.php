<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ExpenseCategories $category */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var float $pendingTotal */
/** @var yii\data\ActiveDataProvider $nilDataProvider */

$this->title = $category->name . ' Expenses';
$this->params['breadcrumbs'][] = ['label' => 'Expense Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-view">

    <!-- Header bar -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card card-<?= $pendingTotal > 0 ? 'warning' : 'success' ?> card-outline">
                <div class="card-body text-center">
                    <p class="mb-1 text-muted">Pending (since last nil)</p>
                    <h2 class="text-<?= $pendingTotal > 0 ? 'warning' : 'success' ?>"><?= number_format($pendingTotal) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-8 d-flex align-items-center">
            <?= Html::a('<i class="fas fa-plus mr-1"></i> Add Expense', ['add', 'category_id' => $category->id], ['class' => 'btn btn-primary mr-2']) ?>
            <?php if ($pendingTotal > 0): ?>
                <?= Html::a('<i class="fas fa-ban mr-1"></i> Nil against Chemical Account', ['nil', 'id' => $category->id], ['class' => 'btn btn-danger']) ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Expense list -->
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">All Expenses — <?= Html::encode($category->name) ?></h3></div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'emptyText'    => 'No expenses recorded yet.',
                'columns' => [
                    [
                        'attribute' => 'created_at',
                        'label'     => 'Date',
                        'value'     => fn($row) => date('d M Y', strtotime($row->created_at)),
                    ],
                    'description',
                    [
                        'label' => 'Paid From',
                        'value' => fn($row) => $row->paidFromAccount ? $row->paidFromAccount->name : '—',
                    ],
                    [
                        'attribute'      => 'amount',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions'  => ['class' => 'text-right'],
                        'value'          => fn($row) => number_format($row->amount),
                    ],
                    [
                        'label'          => '',
                        'format'         => 'raw',
                        'contentOptions' => ['class' => 'text-right'],
                        'value'          => function ($row) {
                            $url   = \yii\helpers\Url::to(['expense/delete-expense', 'id' => $row->id]);
                            $label = ($row->description ?: 'Expense #' . $row->id)
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

    <!-- Nil history -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">Nil History</h3></div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $nilDataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'emptyText'    => 'No nil operations yet.',
                'columns' => [
                    [
                        'attribute' => 'created_at',
                        'label'     => 'Date',
                        'value'     => fn($row) => date('d M Y H:i', strtotime($row->created_at)),
                    ],
                    [
                        'attribute'      => 'chemical_balance_before',
                        'label'          => 'Chemical Before',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions'  => ['class' => 'text-right'],
                        'value'          => fn($row) => number_format($row->chemical_balance_before),
                    ],
                    [
                        'attribute'      => 'expense_total',
                        'label'          => 'Deducted',
                        'contentOptions' => ['class' => 'text-right text-danger'],
                        'headerOptions'  => ['class' => 'text-right'],
                        'value'          => fn($row) => '−' . number_format($row->expense_total),
                    ],
                    [
                        'attribute'      => 'chemical_balance_after',
                        'label'          => 'Chemical After',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions'  => ['class' => 'text-right'],
                        'value'          => fn($row) => number_format($row->chemical_balance_after),
                    ],
                    [
                        'label' => 'By',
                        'value' => fn($row) => $row->performedBy ? $row->performedBy->name : '—',
                    ],
                    'notes',
                ],
            ]); ?>
        </div>
    </div>

</div>
