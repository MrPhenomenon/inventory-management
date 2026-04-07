<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProductUnits $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-units-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'unit_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'conversion_to_base')->textInput(['type' => 'number', 'step' => '0.001']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index', 'product_id' => $model->product_id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
