<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ExpenseCategories $model */

$this->title = 'New Expense Category';
$this->params['breadcrumbs'][] = ['label' => 'Expense Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create-category">
    <div class="card card-primary card-outline" style="max-width:400px;">
        <div class="card-header"><h3 class="card-title">New Category</h3></div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'name')->textInput(['placeholder' => 'e.g. Travel, Salaries...']) ?>
                <div class="form-group mt-3">
                    <?= Html::submitButton('Create', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default ml-2']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
