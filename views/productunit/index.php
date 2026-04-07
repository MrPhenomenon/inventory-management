<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Products $product */

$this->title = 'Units for: ' . $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['/product/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-units-index">

    <div style="margin-bottom: 20px;">
        <p>
            <strong>Product:</strong> <?= $product->name ?> 
            | <strong>Base Unit:</strong> <?= $product->baseUnit ? $product->baseUnit->name : 'N/A' ?>
        </p>
    </div>

    <p>
        <?= Html::a('Add Unit', ['create', 'product_id' => $product->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Products', ['/product/index'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'unit_name',
            [
                'attribute' => 'conversion_to_base',
                'format' => 'decimal',
                'label' => 'Conversion to Base Unit',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function($url, $model) {
                        return Html::a('Edit', $url, ['class' => 'btn btn-warning btn-sm']);
                    },
                    'delete' => function($url, $model) {
                        return Html::a('Delete', $url, [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Are you sure?', 'method' => 'post']
                        ]);
                    },
                ],
                'urlCreator' => function($action, $model, $key, $index) {
                    if ($action === 'update') {
                        return ['productunit/' . $action, 'id' => $model->id];
                    }
                    if ($action === 'delete') {
                        return ['productunit/' . $action, 'id' => $model->id];
                    }
                    return '#';
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
