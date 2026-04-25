<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $filter */

$this->title = 'Parties';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parties-index">

    <p>
        <?= Html::a('Create Party', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="mb-3">
        <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-outline-primary <?= !$filter ? 'active' : '' ?>">All</a>
        <a href="<?= \yii\helpers\Url::to(['index', 'filter' => 'customers']) ?>" class="btn btn-outline-primary <?= $filter === 'customers' ? 'active' : '' ?>">Customers</a>
        <a href="<?= \yii\helpers\Url::to(['index', 'filter' => 'suppliers']) ?>" class="btn btn-outline-primary <?= $filter === 'suppliers' ? 'active' : '' ?>">Suppliers</a>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return $model->displayType();
                },
            ],
            'phone',
            'address:ntext',
            [
                'attribute' => 'opening_balance',
                'label' => 'Opening Balance',
                'contentOptions' => ['style' => 'text-align:right;'],
                'headerOptions' => ['style' => 'text-align:right;'],
                'value' => function ($model) {
                    if (!$model->hasOpeningBalance()) return '-';
                    $label = $model->opening_balance_type === 'receivable' ? 'Receivable' : 'Payable';
                    return Yii::$app->formatter->asDecimal($model->opening_balance, 2) . ' (' . $label . ')';
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('Ledger', $url, ['class' => 'btn btn-info btn-sm']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('Update', $url, ['class' => 'btn btn-warning btn-sm']);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        return ['party/view', 'id' => $model->id];
                    }
                    if ($action === 'update') {
                        return ['party/update', 'id' => $model->id];
                    }
                    return '#';
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
