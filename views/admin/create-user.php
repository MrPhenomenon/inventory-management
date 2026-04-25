<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Tenants;

/** @var app\models\Users $model */
/** @var app\models\Tenants[] $tenants */

$preselectedTenantId = Yii::$app->request->get('tenant_id');

$this->title = 'Add User';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add User';
?>
<div class="admin-create-user">

    <div class="card" style="max-width: 600px;">
        <div class="card-header"><h5 class="mb-0">New User</h5></div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'tenant_id')->dropDownList(
                ArrayHelper::map($tenants, 'id', 'name'),
                ['prompt' => 'Select tenant', 'value' => $preselectedTenantId]
            ) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

            <?= $form->field($model, 'role')->dropDownList($model::optsRole()) ?>

            <div class="form-group">
                <?= Html::label('Password', 'plain_password', ['class' => 'control-label']) ?>
                <?= Html::input('password', 'Users[plain_password]', '', [
                    'class' => 'form-control',
                    'id'    => 'plain_password',
                    'required' => true,
                    'placeholder' => 'Set a password for this user',
                ]) ?>
            </div>

            <div class="form-group mt-3">
                <?= Html::submitButton('Create User', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
