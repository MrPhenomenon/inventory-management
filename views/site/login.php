<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\LoginForm $model */

$this->title = 'Login';
?>
<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to your account</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'email')->textInput([
            'autofocus' => true,
            'placeholder' => 'Email',
            'class' => 'form-control',
        ])->label(false) ?>

        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Password',
            'class' => 'form-control',
        ])->label(false) ?>

        <?= $form->field($model, 'rememberMe')->checkbox(['label' => 'Remember me']) ?>

        <div class="row mt-3">
            <div class="col-12">
                <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary btn-block w-100']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
