<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Expenses $model */
/** @var array $categories  id => name */
/** @var array $accounts    id => "Name (type)" */

$this->title = 'Add Expense';
$this->params['breadcrumbs'][] = ['label' => 'Expense Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-add">
    <div class="card card-primary card-outline" style="max-width:540px;">
        <div class="card-header"><h3 class="card-title">Record Expense</h3></div>
        <div class="card-body">
            <div class="alert alert-info">
                The amount will be <strong>deducted</strong> from the chosen account when saved.
            </div>
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '— Select Category —']) ?>
                <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0.01']) ?>
                <?= $form->field($model, 'description')->textInput(['placeholder' => 'What was this expense for?']) ?>
                <?= $form->field($model, 'paid_from_account_id')->dropDownList($accounts, ['prompt' => '— Paid From —']) ?>
                <div class="form-group mt-3">
                    <?= Html::submitButton('Save Expense', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
