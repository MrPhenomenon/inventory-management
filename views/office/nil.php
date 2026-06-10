<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var float $officeTotal */
/** @var float $chemicalBefore */
/** @var float $chemicalAfter */
/** @var yii\data\ActiveDataProvider $nilDataProvider */

$this->title = 'Nil Operation';
$this->params['breadcrumbs'][] = ['label' => 'Office Account', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="office-nil">

    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card card-danger card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-ban mr-1"></i> Confirm Nil Operation</h3></div>
                <div class="card-body">
                    <?php if ($officeTotal <= 0): ?>
                        <div class="alert alert-info">There are no office expenses to settle.</div>
                    <?php else: ?>
                        <table class="table table-sm table-borderless mb-3">
                            <tr>
                                <td class="text-muted">Chemical Balance Before:</td>
                                <td class="text-right"><strong><?= number_format($chemicalBefore) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Office Total (to deduct):</td>
                                <td class="text-right text-danger"><strong>−<?= number_format($officeTotal) ?></strong></td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted">Chemical Balance After:</td>
                                <td class="text-right <?= $chemicalAfter < 0 ? 'text-danger' : 'text-success' ?>"><strong><?= number_format($chemicalAfter) ?></strong></td>
                            </tr>
                        </table>

                        <?php $form = ActiveForm::begin(['action' => ['nil'], 'method' => 'POST']); ?>
                            <div class="form-group">
                                <label>Notes (optional)</label>
                                <input type="text" name="notes" class="form-control" placeholder="Any remarks...">
                            </div>
                            <div class="form-group">
                                <?= Html::submitButton('<i class="fas fa-check mr-1"></i> Confirm Nil Operation', [
                                    'class' => 'btn btn-danger',
                                    'data-confirm' => 'This will deduct ' . number_format($officeTotal) . ' from the Chemical Account. Are you sure?',
                                ]) ?>
                                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                            </div>
                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">Nil Operation History</h3></div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $nilDataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'emptyText' => 'No nil operations yet.',
                'columns' => [
                    [
                        'attribute' => 'created_at',
                        'label' => 'Date',
                        'value' => fn($row) => date('d M Y H:i', strtotime($row->created_at)),
                    ],
                    [
                        'attribute' => 'chemical_balance_before',
                        'label' => 'Chemical Before',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'value' => fn($row) => number_format($row->chemical_balance_before),
                    ],
                    [
                        'attribute' => 'office_total',
                        'label' => 'Office Deducted',
                        'contentOptions' => ['class' => 'text-right text-danger'],
                        'headerOptions' => ['class' => 'text-right'],
                        'value' => fn($row) => '−' . number_format($row->office_total),
                    ],
                    [
                        'attribute' => 'chemical_balance_after',
                        'label' => 'Chemical After',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'value' => fn($row) => number_format($row->chemical_balance_after),
                    ],
                    [
                        'label' => 'By',
                        'value' => function ($row) {
                            return $row->performedBy ? $row->performedBy->name : 'User #' . $row->performed_by;
                        },
                    ],
                    'notes',
                ],
            ]); ?>
        </div>
    </div>

</div>
