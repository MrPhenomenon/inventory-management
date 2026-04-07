<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sales';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-index">

    <p>
        <?= Html::a('Create Sale', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'customer_id',
                'label' => 'Customer',
                'value' => function ($model) {
                    return $model->customer ? $model->customer->name : 'N/A';
                },
            ],
            [
                'attribute' => 'sale_date',
                'label' => 'Sale Date',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            [
                'attribute' => 'total_amount',
                'label' => 'Total Amount',
                'format' => ['decimal', 2],
            ],
            [
                'attribute' => 'paid_amount',
                'label' => 'Paid Amount',
                'format' => ['decimal', 2],
            ],
            [
                'attribute' => 'status',
                'label' => 'Status',
                'value' => function ($model) {
                    $map = [
                        'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                        'partial' => '<span class="badge bg-info text-white">Partial</span>',
                        'paid' => '<span class="badge bg-success text-white">Paid</span>',
                    ];
                    return $map[$model->status] ?? $model->status;
                },
                'format' => 'html',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', $url, ['class' => 'btn btn-info btn-sm']);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        return ['sales/view', 'id' => $model->id];
                    }
                    return '#';
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
