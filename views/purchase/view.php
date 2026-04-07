<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\models\Purchases;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */

$this->title = 'Purchase #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchases-view">

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%;">Supplier:</th>
                            <td><?= $model->supplier ? $model->supplier->name : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Purchase Date:</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->purchase_date, 'php:Y-m-d H:i') ?></td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:Y-m-d H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Payment Status</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%;">Total Amount:</th>
                            <td style="font-size: 1.2em; font-weight: bold;">
                                <?= Yii::$app->formatter->asDecimal($model->total_amount, 2) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td><?= Yii::$app->formatter->asDecimal($model->paid_amount, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Outstanding:</th>
                            <td><?= Yii::$app->formatter->asDecimal($model->total_amount - $model->paid_amount, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php
                                $statusMap = [
                                    'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                                    'partial' => '<span class="badge bg-info text-white">Partial</span>',
                                    'paid' => '<span class="badge bg-success text-white">Paid</span>',
                                ];
                                echo isset($statusMap[$model->status]) ? $statusMap[$model->status] : $model->status;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4>Purchase Items</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->purchaseItems as $item): ?>
                            <tr>
                                <td><?= $item->product ? $item->product->name : 'N/A' ?></td>
                                <td><?= $item->productUnit ? $item->productUnit->unit_name : 'N/A' ?></td>
                                <td><?= $item->quantity ?></td>
                                <td><?= $item->price ?></td>
                                <td><?= $item->total ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('Back to Purchases', ['index'], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Are you sure?', 'method' => 'post']
        ]) ?>
    </div>

</div>
