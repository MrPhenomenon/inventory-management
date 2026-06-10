<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Balance Sheet';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-sheet">

    <div class="row">

        <div class="col-lg-7">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Assets &amp; Receivables</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">

                        <tbody>
                            <tr class="table-secondary">
                                <td colspan="2"><strong>Chemical Account</strong></td>
                            </tr>
                            <tr>
                                <td class="pl-4">Chemical Account Balance</td>
                                <td class="text-right font-weight-bold text-<?= $chemicalBalance >= 0 ? 'success' : 'danger' ?>">
                                    <?= number_format($chemicalBalance) ?>
                                </td>
                            </tr>
                        </tbody>

                        <?php if (!empty($accountRows)): ?>
                        <tbody>
                            <tr class="table-secondary">
                                <td colspan="2"><strong>Accounts (Cash / Bank / Cheque)</strong></td>
                            </tr>
                            <?php foreach ($accountRows as $row): ?>
                            <tr>
                                <td class="pl-4"><?= Html::encode($row['name']) ?></td>
                                <td class="text-right <?= $row['balance'] >= 0 ? '' : 'text-danger' ?>">
                                    <?= number_format($row['balance']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="font-weight-bold bg-light">
                                <td class="text-right pr-3">Accounts Total</td>
                                <td class="text-right"><?= number_format($totalAccounts) ?></td>
                            </tr>
                        </tbody>
                        <?php endif; ?>
                        <?php if (!empty($receivableRows)): ?>
                        <tbody>
                            <tr class="table-secondary">
                                <td colspan="2"><strong>Party Receivables</strong></td>
                            </tr>
                            <?php foreach ($receivableRows as $row): ?>
                            <tr>
                                <td class="pl-4"><?= Html::encode($row['name']) ?></td>
                                <td class="text-right"><?= number_format($row['balance']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="font-weight-bold bg-light">
                                <td class="text-right pr-3">Receivables Total</td>
                                <td class="text-right"><?= number_format($totalReceivables) ?></td>
                            </tr>
                        </tbody>
                        <?php endif; ?>

                        <?php if (!empty($expenseRows)): ?>
                        <tbody>
                            <tr class="table-secondary">
                                <td colspan="2"><strong>Expense Accounts (Pending)</strong></td>
                            </tr>
                            <?php foreach ($expenseRows as $row): ?>
                            <tr>
                                <td class="pl-4"><?= Html::encode($row['name']) ?></td>
                                <td class="text-right text-warning font-weight-bold"><?= number_format($row['balance']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="font-weight-bold bg-light">
                                <td class="text-right pr-3">Expenses Total</td>
                                <td class="text-right"><?= number_format($totalExpenses) ?></td>
                            </tr>
                        </tbody>
                        <?php endif; ?>

                        <tbody>
                            <tr class="table-primary">
                                <td><strong>TOTAL</strong></td>
                                <td class="text-right"><strong><?= number_format($totalSide1) ?></strong></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Payables</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr class="table-secondary">
                                <td colspan="2"><strong>Party Payables</strong></td>
                            </tr>
                            <?php if (empty($payableRows)): ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No outstanding payables.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($payableRows as $row): ?>
                                <tr>
                                    <td class="pl-4"><?= Html::encode($row['name']) ?></td>
                                    <td class="text-right text-danger font-weight-bold"><?= number_format($row['balance']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <tr class="table-danger">
                                <td><strong>TOTAL</strong></td>
                                <td class="text-right"><strong><?= number_format($totalSide2) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            $netClass = $netDifference >= 0 ? 'success' : 'danger';
            $netIcon  = $netDifference >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            ?>
            <div class="card card-outline card-<?= $netClass ?>">
                <div class="card-body text-center py-4">
                    <p class="mb-1 text-muted">Net Position</p>
                    <h2 class="text-<?= $netClass ?>">
                        <i class="fas <?= $netIcon ?>"></i>
                        <?= number_format(abs($netDifference)) ?>
                    </h2>
                    
                </div>
            </div>

        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <small class="text-muted">
                Generated on <?= date('d M Y, H:i') ?>.
                Chemical Account uses: opening balance + purchases − sales − nil deductions.
                Expense accounts show pending totals (since last nil operation).
            </small>
        </div>
    </div>

</div>
