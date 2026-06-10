<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var float $chemicalBalance */
/** @var array $accountData */

$this->title = 'Accounts';
$this->params['breadcrumbs'][] = $this->title;

$balanceColor = $chemicalBalance >= 0 ? 'success' : 'danger';
?>
<div class="account-index">

    <!-- Chemical Account Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-<?= $balanceColor ?> card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-flask mr-1"></i> Chemical Account</h3>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-cog"></i>', ['chemical'], ['class' => 'btn btn-tool', 'title' => 'Configure']) ?>
                    </div>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-<?= $balanceColor ?>"><?= number_format($chemicalBalance) ?></h2>
                    <p class="text-muted mb-0" style="font-size:0.85em;">opening + purchases − sales − nil deductions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub-accounts -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sub-Accounts</h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-plus"></i> New Account', ['create'], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a('<i class="fas fa-exchange-alt"></i> Transfer', ['transfer'], ['class' => 'btn btn-sm btn-warning ml-1']) ?>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Type</th>
                        <th class="text-right">Balance</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($accountData)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">No accounts yet. <a href="<?= Url::to(['create']) ?>">Create one</a>.</td></tr>
                <?php else: ?>
                    <?php foreach ($accountData as $row): ?>
                    <tr>
                        <td><?= Html::encode($row['name']) ?></td>
                        <td><span class="badge badge-secondary"><?= Html::encode($row['type']) ?></span></td>
                        <td class="text-right <?= $row['balance'] < 0 ? 'text-danger' : '' ?>">
                            <strong><?= number_format($row['balance']) ?></strong>
                        </td>
                        <td class="text-right">
                            <?= Html::a('Ledger', ['view', 'id' => $row['id']], ['class' => 'btn btn-xs btn-info']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
