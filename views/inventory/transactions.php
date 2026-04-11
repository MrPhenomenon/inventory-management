<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $productList */
/** @var string $productId */
/** @var string $dateFrom */
/** @var string $dateTo */

$this->title = 'Inventory Transactions';
$this->params['breadcrumbs'][] = ['label' => 'Inventory', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-transactions">

    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Filters</h5></div>
        <div class="card-body">
            <form method="get" class="form-inline">
                <div class="form-group mr-3">
                    <label for="product_id" class="mr-2">Product:</label>
                    <select id="product_id" name="product_id" class="form-control">
                        <option value="">All Products</option>
                        <?php foreach ($productList as $id => $name): ?>
                            <option value="<?= $id ?>" <?= $productId == $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label for="date_from" class="mr-2">From:</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="<?= Html::encode($dateFrom) ?>">
                </div>
                <div class="form-group mr-3">
                    <label for="date_to" class="mr-2">To:</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="<?= Html::encode($dateTo) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'product_id',
                'label' => 'Product',
                'value' => function ($model) {
                    return $model->product ? $model->product->name : 'N/A';
                },
            ],
            [
                'attribute' => 'type',
                'label' => 'Type',
                'value' => function ($model) {
                    $typeMap = [
                        'in' => '<span class="badge bg-success">IN</span>',
                        'out' => '<span class="badge bg-danger">OUT</span>',
                        'adjustment' => '<span class="badge bg-warning">ADJUSTMENT</span>',
                    ];
                    return isset($typeMap[$model->type]) ? $typeMap[$model->type] : $model->type;
                },
                'format' => 'html',
            ],
            'quantity',
            [
                'attribute' => 'product_unit_id',
                'label' => 'Unit',
                'value' => function ($model) {
                    return $model->productUnit ? $model->productUnit->unit_name : 'N/A';
                },
            ],
            [
                'attribute' => 'base_quantity',
                'label' => 'Base Qty',
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'reference_type',
                'label' => 'Reference',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Date',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
