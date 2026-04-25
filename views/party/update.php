<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Parties $model */

$this->title = 'Update Party: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Parties', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="parties-update">

    <div class="party-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'type')->dropDownList($model::optsType(), ['prompt' => 'Select type']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'opening_balance')->textInput(['type' => 'number', 'min' => 0, 'step' => '0.01', 'id' => 'parties-opening_balance'])->hint('Outstanding balance this party had before being added to the system.') ?>
            </div>
            <div class="col-md-6" id="opening-balance-type-wrapper" style="<?= (float)$model->opening_balance > 0 ? '' : 'display:none;' ?>">
                <?= $form->field($model, 'opening_balance_type')->dropDownList($model::optsOpeningBalanceType(), ['prompt' => 'Select direction']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
<?php
$js = <<<JS
document.getElementById('parties-opening_balance').addEventListener('input', function () {
    var wrapper = document.getElementById('opening-balance-type-wrapper');
    wrapper.style.display = parseFloat(this.value) > 0 ? '' : 'none';
    if (parseFloat(this.value) <= 0) {
        document.getElementById('parties-opening_balance_type').value = '';
    }
});
JS;
$this->registerJs($js);
?>
