<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ChemicalAccount $model */
/** @var float $balance */

$this->title = 'Chemical Account';
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$balanceColor = $balance >= 0 ? 'success' : 'danger';
?>
<div class="account-chemical">
    <div class="row">
        <div class="col-md-5">
            <div class="card card-<?= $balanceColor ?> card-outline mb-3">
                <div class="card-body text-center">
                    <p class="mb-1 text-muted">Current Chemical Account Balance</p>
                    <h2 class="text-<?= $balanceColor ?>"><?= number_format($balance) ?></h2>
                    <small class="text-muted">opening balance + incoming payments − outgoing payments − nil operations</small>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Set Opening Balance</h3></div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>
                        <?= $form->field($model, 'opening_balance')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
                        <div class="form-group mt-3">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
