<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\Tenants $model */

$this->title = 'Add Tenant';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add Tenant';
?>
<div class="admin-create-tenant">

    <div class="card" style="max-width: 600px;">
        <div class="card-header"><h5 class="mb-0">New Tenant</h5></div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Business name']) ?>
            <?= $form->field($model, 'email')->textInput(['type' => 'email', 'placeholder' => 'Contact email']) ?>
            <?= $form->field($model, 'status')->dropDownList($model::optsStatus()) ?>

            <div class="form-group mt-3">
                <?= Html::submitButton('Create Tenant', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
