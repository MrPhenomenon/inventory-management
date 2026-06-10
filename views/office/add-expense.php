<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\OfficeExpenses $model */

$this->title = 'Add Office Expense';
$this->params['breadcrumbs'][] = ['label' => 'Office Account', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="office-add-expense">
    <div class="card card-primary card-outline" style="max-width:480px;">
        <div class="card-header"><h3 class="card-title">Add Expense</h3></div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0.01']) ?>
                <?= $form->field($model, 'description')->textInput(['placeholder' => 'What was this expense for?']) ?>
                <div class="form-group mt-3">
                    <?= Html::submitButton('Save Expense', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
