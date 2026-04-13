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
/** @var array $transactions */

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
                        <?php if (($model->isTypeCustomer() || $model->isTypeBoth()) && ($model->isTypeSupplier() || $model->isTypeBoth())): ?>
                        <tr style="border-top: 2px solid #dee2e6;">
                            <th>Net Balance:</th>
                            <td style="text-align: right; font-weight: bold; color: <?= ($customerBalance - $supplierBalance) > 0 ? 'green' : (($customerBalance - $supplierBalance) < 0 ? 'red' : 'gray') ?>;">
                                <?= Yii::$app->formatter->asDecimal($customerBalance - $supplierBalance, 2) ?>
                                <span style="font-size: 0.75em; font-weight: normal; color: #666;">
                                    <?php 
                                    $netBalance = $customerBalance - $supplierBalance;
                                    if ($netBalance > 0) {
                                        echo '(They owe you)';
                                    } elseif ($netBalance < 0) {
                                        echo '(You owe them)';
                                    } else {
                                        echo '(Balanced)';
                                    }
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Ledger Section -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Complete Ledger</h5></div>
        <div class="card-body" style="overflow-x: auto;">
            <?php if (count($transactions) > 0): ?>
            <table class="table table-striped table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th style="text-align: right;">Debit</th>
                        <th style="text-align: right;">Credit</th>
                        <th style="text-align: right;">Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // echo '<pre>';
                    // print_r($transactions);
                    // echo '</pre>';
                    // die;
                    $runningBalance = 0;
                    foreach ($transactions as $transaction):
                        $runningBalance += $transaction['debit'] - $transaction['credit'];
                        $isPaymentRow = isset($transaction['sort_order']) && $transaction['sort_order'] === 1;
                    ?>
                    <tr style="<?= $isPaymentRow ? 'background-color: #f9f9f9; font-size: 0.95em;' : '' ?>">
                        <td><?= Yii::$app->formatter->asDate($transaction['date'], 'php:Y-m-d') ?></td>
                        <td>
                            <?php
                            if (str_contains($transaction['type'], 'Payment')) {
                                $badgeClass = 'badge-secondary';
                            } elseif ($transaction['type'] === 'Sale') {
                                $badgeClass = 'badge-success';
                            } else {
                                $badgeClass = 'badge-primary';
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= Html::encode($transaction['type']) ?></span>
                        </td>
                        <td style="<?= $isPaymentRow ? 'padding-left: 2rem;' : '' ?>"><?= Html::encode($transaction['description']) ?></td>
                        <td style="text-align: right;">
                            <?= $transaction['debit'] > 0 ? Yii::$app->formatter->asDecimal($transaction['debit'], 2) : '-' ?>
                        </td>
                        <td style="text-align: right;">
                            <?= $transaction['credit'] > 0 ? Yii::$app->formatter->asDecimal($transaction['credit'], 2) : '-' ?>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            <?= Yii::$app->formatter->asDecimal($runningBalance, 2) ?>
                        </td>
                        <td>
                            <?php
                            $statusBadgeClass = match($transaction['status']) {
                                'paid' => 'badge-success',
                                'partial' => 'badge-warning',
                                'pending' => 'badge-info',
                                default => 'badge-secondary',
                            };
                            ?>
                            <span class="badge <?= $statusBadgeClass ?>"><?= ucfirst(Html::encode($transaction['status'])) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" style="font-weight: bold;">Final Balance</td>
                        <td style="text-align: right; font-weight: bold;">
                            <?= Yii::$app->formatter->asDecimal($totalSales + $totalOutgoingPayments, 2) ?>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            <?= Yii::$app->formatter->asDecimal($totalPurchases + $totalIncomingPayments, 2) ?>
                        </td>
                        <td style="text-align: right; font-weight: bold; color: <?= ($totalSales + $totalOutgoingPayments - $totalPurchases - $totalIncomingPayments) > 0 ? 'red' : 'green' ?>;">
                            <?= Yii::$app->formatter->asDecimal($totalSales + $totalOutgoingPayments - $totalPurchases - $totalIncomingPayments, 2) ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <?php else: ?>
            <p class="text-muted">No transactions yet.</p>
            <?php endif; ?>
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
