<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Chemical Account Ledger';
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$typeLabels = [
    'purchase' => '<span class="badge badge-success">Purchase</span>',
    'sale'     => '<span class="badge badge-danger">Sale</span>',
    'nil'      => '<span class="badge badge-warning">Nil</span>',
];
?>
<div class="chemical-ledger">

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card card-outline card-<?= $currentBalance >= 0 ? 'success' : 'danger' ?>">
                <div class="card-body text-center">
                    <p class="mb-1 text-muted">Current Balance</p>
                    <h4 class="text-<?= $currentBalance >= 0 ? 'success' : 'danger' ?>"><?= number_format($currentBalance) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ledger Entries</h3>
            <div class="card-tools">
                <span class="badge badge-success mr-1">Purchase = Credit</span>
                <span class="badge badge-danger mr-1">Sale = Debit</span>
                <span class="badge badge-warning">Nil = Debit</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:110px;">Date</th>
                            <th style="width:80px;">Type</th>
                            <th style="width:160px;">Party</th>
                            <th>Items</th>
                            <th class="text-right" style="width:110px;">Credit (+)</th>
                            <th class="text-right" style="width:110px;">Debit (−)</th>
                            <th class="text-right" style="width:120px;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Opening Balance row -->
                        <tr class="table-active font-weight-bold">
                            <td colspan="6" class="text-right">Opening Balance</td>
                            <td class="text-right"><?= number_format($openingBalance) ?></td>
                        </tr>

                        <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No transactions yet.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                            <tr class="<?= $row['type'] === 'purchase' ? 'table-success-light' : ($row['type'] === 'sale' ? 'table-danger-light' : 'table-warning-light') ?>">
                                <td><?= Html::encode(date('d M Y', strtotime($row['date']))) ?></td>
                                <td><?= $typeLabels[$row['type']] ?? $row['type'] ?></td>
                                <td>
                                    <?php if ($row['party_id']): ?>
                                        <?= Html::a(Html::encode($row['party_name']), ['/party/view', 'id' => $row['party_id']], ['class' => 'text-primary font-weight-bold']) ?>
                                    <?php else: ?>
                                        <?= Html::encode($row['party_name']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted" style="font-size:0.88em;">
                                    <?= Html::encode($row['items_desc'] ?? '') ?>
                                </td>
                                <td class="text-right <?= $row['credit'] > 0 ? 'text-success font-weight-bold' : 'text-muted' ?>">
                                    <?= $row['credit'] > 0 ? number_format($row['credit']) : '—' ?>
                                </td>
                                <td class="text-right <?= $row['debit'] > 0 ? 'text-danger font-weight-bold' : 'text-muted' ?>">
                                    <?= $row['debit'] > 0 ? number_format($row['debit']) : '—' ?>
                                </td>
                                <td class="text-right font-weight-bold <?= $row['balance'] >= 0 ? 'text-dark' : 'text-danger' ?>">
                                    <?= number_format($row['balance']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Closing Balance row -->
                        <tr class="table-active font-weight-bold">
                            <td colspan="6" class="text-right">Closing Balance</td>
                            <td class="text-right text-<?= $currentBalance >= 0 ? 'success' : 'danger' ?>"><?= number_format($currentBalance) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
.table-success-light { background-color: rgba(40,167,69,.06); }
.table-danger-light  { background-color: rgba(220,53,69,.06); }
.table-warning-light { background-color: rgba(255,193,7,.06); }
</style>
