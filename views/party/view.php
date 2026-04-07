<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Parties $model */
/** @var float $totalSales */
/** @var float $totalPurchases */
/** @var float $totalIncomingPayments */
/** @var float $totalOutgoingPayments */
/** @var float $customerBalance */
/** @var float $supplierBalance */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Parties', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parties-view">

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Party Information</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%;">Name:</th>
                            <td><?= Html::encode($model->name) ?></td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><?= Html::encode($model->displayType()) ?></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><?= Html::encode($model->phone ?: 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td><?= Html::encode($model->address ?: 'N/A') ?></td>
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
                <div class="card-header"><h5 class="mb-0">Ledger Summary</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Total Sales:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalSales, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Total Purchases:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalPurchases, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Incoming Payments:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalIncomingPayments, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Outgoing Payments:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalOutgoingPayments, 2) ?></td>
                        </tr>
                        <?php if ($model->isTypeCustomer() || $model->isTypeBoth()): ?>
                        <tr>
                            <th>Customer Balance (Owed by them):</th>
                            <td style="text-align: right; font-weight: bold; color: <?= $customerBalance > 0 ? 'red' : 'green' ?>;">
                                <?= Yii::$app->formatter->asDecimal($customerBalance, 2) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($model->isTypeSupplier() || $model->isTypeBoth()): ?>
                        <tr>
                            <th>Supplier Balance (Owed to them):</th>
                            <td style="text-align: right; font-weight: bold; color: <?= $supplierBalance > 0 ? 'red' : 'green' ?>;">
                                <?= Yii::$app->formatter->asDecimal($supplierBalance, 2) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Are you sure you want to delete this party?', 'method' => 'post']
        ]) ?>
        <?= Html::a('Back to Parties', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

</div>
