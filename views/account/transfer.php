<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AccountTransfers $model */
/** @var array $accountList */

$this->title = 'Transfer Between Accounts';
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-transfer">
    <div class="card card-warning card-outline" style="max-width:520px;">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> Transfer Funds</h3></div>
        <div class="card-body">
            <div class="alert alert-info">
                Transfers move money between sub-accounts only. The <strong>Chemical Account</strong> balance is not affected.
            </div>
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'from_account_id')->dropDownList($accountList, ['prompt' => '— From Account —']) ?>
                <?= $form->field($model, 'to_account_id')->dropDownList($accountList, ['prompt' => '— To Account —']) ?>
                <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0.01']) ?>
                <?= $form->field($model, 'description')->textInput(['placeholder' => 'Optional description']) ?>
                <div class="form-group mt-3">
                    <?= Html::submitButton('<i class="fas fa-check"></i> Confirm Transfer', ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
