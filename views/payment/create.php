<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Accounts;
use app\models\Parties;
use app\models\Sales;
use app\models\Purchases;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */

$this->title = 'Record Payment';
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$partyId = Yii::$app->request->get('party_id');
$saleId = Yii::$app->request->get('sale_id');
$purchaseId = Yii::$app->request->get('purchase_id');

if ($saleId) {
    $sale = Sales::findOne($saleId);
    $model->party_id = $sale->customer_id;
    $model->type = 'incoming';
    $model->reference_type = 'sale';
    $model->reference_id = $saleId;
} elseif ($purchaseId) {
    $purchase = Purchases::findOne($purchaseId);
    $model->party_id = $purchase->supplier_id;
    $model->type = 'outgoing';
    $model->reference_type = 'purchase';
    $model->reference_id = $purchaseId;
} elseif ($partyId) {
    $model->party_id = $partyId;
}

$parties = Parties::find()->orderBy(['name' => SORT_ASC])->all();
$partyList = [];
foreach ($parties as $party) {
    $balance = $party->getCurrentBalance();
    if ($balance === 0) {
        $balanceText = 'Balanced';
    } elseif ($balance > 0) {
        $balanceText = 'Receivable ' . Yii::$app->formatter->asDecimal(abs($balance));
    } else {
        $balanceText = 'Payable ' . Yii::$app->formatter->asDecimal(abs($balance));
    }

    $partyList[$party->id] = $party->name . ' (' . $balanceText . ')';
}

$accounts = Accounts::find()->orderBy(['name' => SORT_ASC])->all();
$accountList = [];
foreach ($accounts as $acc) {
    $accountList[$acc->id] = $acc->name . ' (' . $acc->displayType() . ')';
}

?>
<div class="payment-create">

    <div class="payment-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'party_id')->dropDownList($partyList, ['prompt' => 'Select a party']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'type')->dropDownList($model::optsType(), ['prompt' => 'Select type']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'placeholder' => '0.00']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'account_id')->dropDownList($accountList, ['prompt' => '— No specific account —']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'notes')->textInput(['placeholder' => 'Optional notes']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Record Payment', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>