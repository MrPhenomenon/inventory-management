<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Accounts $model */

$this->title = 'New Account';
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-create">
    <div class="card card-primary card-outline" style="max-width:500px;">
        <div class="card-header"><h3 class="card-title">New Sub-Account</h3></div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'name')->textInput(['placeholder' => 'e.g. Meezan Bank, Petty Cash']) ?>
                <?= $form->field($model, 'type')->dropDownList($model::optsType(), ['prompt' => '— Select Type —']) ?>
                <?= $form->field($model, 'opening_balance')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
                <div class="form-group mt-3">
                    <?= Html::submitButton('Create Account', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
