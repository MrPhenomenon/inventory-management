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
/** @var float $openingBalance */
/** @var string|null $openingBalanceType */
/** @var array $transactions */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Parties', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
@media print {
    body * { visibility: hidden; }
    #ledger-print-area,
    #ledger-print-area * { visibility: visible; }
    #ledger-print-area {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        padding: 20px;
    }

    #ledger-print-area .card { border: none; box-shadow: none; }
    #ledger-print-area .card-header { border-bottom: 2px solid #333; padding: 0 0 8px 0; margin-bottom: 12px; }
    #ledger-print-area .no-print { display: none !important; }

    #ledger-print-area table { width: 100%; border-collapse: collapse; font-size: 11pt; }
    #ledger-print-area th,
    #ledger-print-area td { border: 1px solid #ccc; padding: 5px 8px; }
    #ledger-print-area thead th { background-color: #f0f0f0 !important; -webkit-print-color-adjust: exact; }
    #ledger-print-area .badge { border: 1px solid #333; background: none !important; color: #000 !important; padding: 1px 4px; font-size: 9pt; }
}
</style>

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
                        <?php if ($model->hasOpeningBalance()): ?>
                        <tr>
                            <th>Opening Balance:</th>
                            <td>
                                <?= Yii::$app->formatter->asDecimal($model->opening_balance) ?>
                                <span class="badge <?= $model->opening_balance_type === 'receivable' ? 'badge-warning' : 'badge-info' ?>" style="margin-left:4px;">
                                    <?= $model->opening_balance_type === 'receivable' ? 'Receivable' : 'Payable' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Ledger Summary</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <?php if ($model->hasOpeningBalance()): ?>
                        <tr>
                            <th>Opening Balance <span style="font-weight:normal;font-size:0.85em;">(<?= $model->opening_balance_type === 'receivable' ? 'Receivable' : 'Payable' ?>)</span>:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($model->opening_balance) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Total Sales:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalSales) ?></td>
                        </tr>
                        <tr>
                            <th>Total Purchases:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalPurchases) ?></td>
                        </tr>
                        <tr>
                            <th>Incoming Payments:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalIncomingPayments) ?></td>
                        </tr>
                        <tr>
                            <th>Outgoing Payments:</th>
                            <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalOutgoingPayments) ?></td>
                        </tr>
                        <?php if ($model->isTypeCustomer() || $model->isTypeBoth()): ?>
                        <tr>
                            <th>Customer Balance (Owed by them):</th>
                            <td style="text-align: right; font-weight: bold; color: <?= $customerBalance > 0 ? 'red' : 'green' ?>;">
                                <?= Yii::$app->formatter->asDecimal($customerBalance) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($model->isTypeSupplier() || $model->isTypeBoth()): ?>
                        <tr>
                            <th>Supplier Balance (Owed to them):</th>
                            <td style="text-align: right; font-weight: bold; color: <?= $supplierBalance > 0 ? 'red' : 'green' ?>;">
                                <?= Yii::$app->formatter->asDecimal($supplierBalance) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if (($model->isTypeCustomer() || $model->isTypeBoth()) && ($model->isTypeSupplier() || $model->isTypeBoth())): ?>
                        <tr style="border-top: 2px solid #dee2e6;">
                            <th>Net Balance:</th>
                            <td style="text-align: right; font-weight: bold; color: <?= ($customerBalance - $supplierBalance) > 0 ? 'green' : (($customerBalance - $supplierBalance) < 0 ? 'red' : 'gray') ?>;">
                                <?= Yii::$app->formatter->asDecimal($customerBalance - $supplierBalance) ?>
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

    <div class="card mb-3" id="ledger-print-area">
        <div style="display:none;" class="print-only-header">
            <h4 style="margin:0 0 4px 0;"><?= Html::encode($model->name) ?> — Ledger</h4>
            <p style="margin:0 0 4px 0; font-size:10pt; color:#555;">
                <?= Html::encode($model->phone ?: '') ?>
                <?= $model->address ? ' · ' . Html::encode($model->address) : '' ?>
            </p>
            <p style="margin:0; font-size:9pt; color:#888;">Printed: <span id="printDate"></span></p>
        </div>
        <style>@media print { .print-only-header { display: block !important; } }</style>

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Complete Ledger</h5>
            <?= \yii\helpers\Html::a(
                '<i class="fas fa-file-pdf mr-1"></i>Save as PDF',
                ['ledger-pdf', 'id' => $model->id],
                ['class' => 'btn btn-sm btn-primary no-print', 'target' => '_blank']
            ) ?>
        </div>
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
                            if ($transaction['type'] === 'Opening Balance') {
                                $badgeClass = 'badge-dark';
                            } elseif (str_contains($transaction['type'], 'Payment')) {
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
                            <?= $transaction['debit'] > 0 ? Yii::$app->formatter->asDecimal($transaction['debit']) : '-' ?>
                        </td>
                        <td style="text-align: right;">
                            <?= $transaction['credit'] > 0 ? Yii::$app->formatter->asDecimal($transaction['credit']) : '-' ?>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            <?= Yii::$app->formatter->asDecimal($runningBalance) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <?php
                        $footerDebit  = $totalSales + $totalOutgoingPayments
                            + ($openingBalanceType === 'receivable' ? $openingBalance : 0);
                        $footerCredit = $totalPurchases + $totalIncomingPayments
                            + ($openingBalanceType === 'payable' ? $openingBalance : 0);
                        $footerNet    = $footerDebit - $footerCredit;
                    ?>
                    <tr>
                        <td colspan="3" style="font-weight: bold;">Final Balance</td>
                        <td style="text-align: right; font-weight: bold;">
                            <?= Yii::$app->formatter->asDecimal($footerDebit) ?>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            <?= Yii::$app->formatter->asDecimal($footerCredit) ?>
                        </td>
                        <td style="text-align: right; font-weight: bold; color: <?= $footerNet > 0 ? 'red' : 'green' ?>;">
                            <?= Yii::$app->formatter->asDecimal($footerNet) ?>
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
