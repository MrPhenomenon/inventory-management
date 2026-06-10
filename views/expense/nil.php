<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ExpenseCategories $category */
/** @var float $expenseTotal */
/** @var float $chemicalBefore */
/** @var float $chemicalAfter */

$this->title = 'Nil — ' . $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Expense Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['view', 'id' => $category->id]];
$this->params['breadcrumbs'][] = 'Nil Operation';
?>
<div class="expense-nil">
    <div class="row">
        <div class="col-md-5">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ban mr-1"></i>
                        Nil "<?= Html::encode($category->name) ?>" against Chemical Account
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td class="text-muted">Chemical Balance Before:</td>
                            <td class="text-right"><strong><?= number_format($chemicalBefore) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><?= Html::encode($category->name) ?> Expenses (to add):</td>
                            <td class="text-right text-success"><strong>+<?= number_format($expenseTotal) ?></strong></td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted">Chemical Balance After:</td>
                            <td class="text-right <?= $chemicalAfter < 0 ? 'text-danger' : 'text-success' ?>">
                                <strong><?= number_format($chemicalAfter) ?></strong>
                            </td>
                        </tr>
                    </table>

                    <?php $form = ActiveForm::begin(['action' => ['nil', 'id' => $category->id], 'method' => 'POST']); ?>
                        <div class="form-group">
                            <label>Notes <span class="text-muted">(optional)</span></label>
                            <input type="text" name="notes" class="form-control" placeholder="Any remarks...">
                        </div>
                        <div class="form-group">
                            <?= Html::submitButton('<i class="fas fa-check mr-1"></i> Confirm Nil', [
                                'class'        => 'btn btn-danger',
                                'data-confirm' => 'Add ' . number_format($expenseTotal)
                                    . ' to Chemical Account for "' . $category->name . '"? This cannot be undone.',
                            ]) ?>
                            <?= Html::a('Cancel', ['view', 'id' => $category->id], ['class' => 'btn btn-default ml-2']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
