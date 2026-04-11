<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Stock Levels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-index">

    <div class="mb-3">
        <?= Html::a('Adjust Stock', ['adjust'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('View Transactions', ['transactions'], ['class' => 'btn btn-info']) ?>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => 'Product Name',
                'value' => function ($data) {
                    return Html::a($data['name'], ['product/view', 'id' => $data['id']], ['target' => '_blank']);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'category',
                'label' => 'Category',
            ],
            [
                'attribute' => 'base_stock',
                'label' => 'Current Stock (Base Unit)',
                'value' => function ($data) {
                    $stock = $data['base_stock'];
                    $color = $stock > 0 ? 'green' : ($stock < 0 ? 'red' : 'gray');
                    return "<span style=\"color: {$color}; font-weight: bold;\">" . number_format($stock, 2) . "</span>";
                },
                'format' => 'html',
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'base_unit',
                'label' => 'Base Unit',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $data) {
                        return Html::a('View Details', ['product/view', 'id' => $data['id']], ['class' => 'btn btn-info btn-sm']);
                    },
                ],
                'urlCreator' => function ($action, $data, $key, $index) {
                    return '#';
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
