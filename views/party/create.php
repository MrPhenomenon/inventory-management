<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Parties $model */

$this->title = 'Create Party';
$this->params['breadcrumbs'][] = ['label' => 'Parties', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parties-create">

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
                <?= $form->field($model, 'opening_balance')->textInput(['type' => 'number', 'min' => 0, 'step' => '0.01', 'id' => 'parties-opening_balance'])->hint('Leave 0 if no outstanding balance exists at the time of adding this party.') ?>
            </div>
            <div class="col-md-6" id="opening-balance-type-wrapper" style="<?= (float)$model->opening_balance > 0 ? '' : 'display:none;' ?>">
                <?= $form->field($model, 'opening_balance_type')->dropDownList($model::optsOpeningBalanceType(), ['prompt' => 'Select direction']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
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
