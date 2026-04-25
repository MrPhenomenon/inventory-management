<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Products $searchModel */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="products-index">
    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <div class="product-search"
        style="margin-bottom: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 4px;">
        <h4>Search / Filter</h4>
        <?php $form = \yii\widgets\ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'options' => ['data-pjax' => true]
        ]); ?>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($searchModel, 'name')->textInput(['placeholder' => 'Search by name']) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($searchModel, 'category')->textInput(['placeholder' => 'Search by category']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'category',
            [
                'attribute' => 'base_unit_id',
                'label' => 'Base Unit',
                'value' => function ($model) {
                return $model->baseUnit ? $model->baseUnit->name : 'N/A';
            }
            ],
            'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {units}',
                'buttons' => [
                    'view' => function ($url, $model) {
                    return Html::a('View', $url, ['class' => 'btn btn-info btn-sm']);
                },
                    'update' => function ($url, $model) {
                    return Html::a('Edit', $url, ['class' => 'btn btn-warning btn-sm']);
                },
                    'units' => function ($url, $model) {
                    return Html::a(
                        'Units',
                        ['/productunit/index', 'product_id' => $model->id],
                        [
                            'class' => 'btn btn-primary btn-sm',
                            'data-pjax' => 0
                        ]
                    );
                },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'update') {
                    return ['product/' . $action, 'id' => $model->id];
                }
                return '#';
            }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>