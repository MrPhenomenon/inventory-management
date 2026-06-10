<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var float $officeTotal */
/** @var float $chemicalBalance */

$this->title = 'Office Account';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="office-index">

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-warning card-outline">
                <div class="card-body text-center">
                    <p class="mb-1 text-muted">Office Total (Since Last Nil)</p>
                    <h2 class="text-warning"><?= number_format($officeTotal) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-<?= $chemicalBalance >= 0 ? 'success' : 'danger' ?> card-outline">
                <div class="card-body text-center">
                    <p class="mb-1 text-muted">Chemical Account Balance</p>
                    <h2 class="text-<?= $chemicalBalance >= 0 ? 'success' : 'danger' ?>"><?= number_format($chemicalBalance) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <?= Html::a('<i class="fas fa-plus mr-1"></i> Add Expense', ['add-expense'], ['class' => 'btn btn-primary mr-2']) ?>
            <?= Html::a('<i class="fas fa-ban mr-1"></i> Nil Operation', ['nil'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Office Expenses</h3></div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'columns' => [
                    [
                        'attribute' => 'created_at',
                        'label' => 'Date',
                        'value' => fn($row) => date('d M Y', strtotime($row->created_at)),
                    ],
                    'description',
                    [
                        'attribute' => 'amount',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'value' => fn($row) => number_format($row->amount),
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
