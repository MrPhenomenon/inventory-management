<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Purchases;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchases-index">

    <p>
        <?= Html::a('Create Purchase', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'supplier_id',
                'label' => 'Supplier',
                'value' => function($model) {
                    return $model->supplier ? $model->supplier->name : 'N/A';
                }
            ],
            [
                'attribute' => 'purchase_date',
                'label' => 'Purchase Date',
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
                'value' => function($model) {
                    $statusMap = [
                        'pending' => '<span class="badge bg-warning">Pending</span>',
                        'partial' => '<span class="badge bg-info">Partial</span>',
                        'paid' => '<span class="badge bg-success">Paid</span>',
                    ];
                    return isset($statusMap[$model->status]) ? $statusMap[$model->status] : $model->status;
                },
                'format' => 'html',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('View', $url, ['class' => 'btn btn-info btn-sm']);
                    },
                    
                ],
                'urlCreator' => function($action, $model, $key, $index) {
                    if ($action === 'view') {
                        return ['purchase/view', 'id' => $model->id];
                    }
                    return '#';
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
