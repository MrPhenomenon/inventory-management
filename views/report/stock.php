<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Stock Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-stock">

    <p>Current inventory levels across all products.</p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => 'Product Name',
            ],
            [
                'attribute' => 'category',
                'label' => 'Category',
            ],
            [
                'attribute' => 'base_stock',
                'label' => 'Current Stock',
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
                'label' => 'Unit',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
