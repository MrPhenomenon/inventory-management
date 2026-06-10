<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $categoryData */
/** @var float $chemicalBalance */

$this->title = 'Expense Accounts';
$this->params['breadcrumbs'][] = $this->title;

$balColor = $chemicalBalance >= 0 ? 'success' : 'danger';
?>
<div class="expense-index">

    <!-- Chemical Account summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-<?= $balColor ?> card-outline">
                <div class="card-body text-center">
                    <p class="mb-1 text-muted">Chemical Account Balance</p>
                    <h2 class="text-<?= $balColor ?>"><?= number_format($chemicalBalance) ?></h2>
                    <small class="text-muted">opening + purchases − sales − nil deductions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Category cards -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Expense Categories</h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-plus"></i> New Category', ['create-category'], ['class' => 'btn btn-sm btn-primary']) ?>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-right">Pending (since last nil)</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($categoryData)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-3">No categories yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($categoryData as $row): ?>
                    <tr>
                        <td>
                            <strong><?= Html::encode($row['name']) ?></strong>
                        </td>
                        <td class="text-right <?= $row['pending_total'] > 0 ? 'text-warning' : 'text-muted' ?>">
                            <strong><?= number_format($row['pending_total']) ?></strong>
                        </td>
                        <td class="text-right">
                            <?= Html::a('View', ['view', 'id' => $row['id']], ['class' => 'btn btn-sm btn-info']) ?>
                            <?= Html::a('Add Expense', ['add', 'category_id' => $row['id']], ['class' => 'btn btn-sm btn-primary ml-1']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
