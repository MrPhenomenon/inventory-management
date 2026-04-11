<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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

$parties = Parties::find()->asArray()->all();
$partyList = array_combine(
    array_column($parties, 'id'),
    array_column($parties, 'name')
);

$sales = Sales::find()->where(['!=', 'status', Sales::STATUS_PAID])->asArray()->all();
$salesList = array_combine(
    array_column($sales, 'id'),
    array_map(function ($s) { return 'Sale #' . $s['id'] . ' - ' . Yii::$app->formatter->asDecimal($s['total_amount'] - $s['paid_amount'], 2) . ' remaining'; }, $sales)
);

$purchases = Purchases::find()->where(['!=', 'status', Purchases::STATUS_PAID])->asArray()->all();
$purchasesList = array_combine(
    array_column($purchases, 'id'),
    array_map(function ($p) { return 'Purchase #' . $p['id'] . ' - ' . Yii::$app->formatter->asDecimal($p['total_amount'] - $p['paid_amount'], 2) . ' remaining'; }, $purchases)
);
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
                <?= $form->field($model, 'method')->dropDownList($model::optsMethod(), ['prompt' => 'Select method']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'reference_type')->dropDownList(['sale' => 'Sale', 'purchase' => 'Purchase', '' => 'None'], ['prompt' => 'Select reference (optional)']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'reference_id')->dropDownList([], ['prompt' => 'Select document', 'id' => 'reference-id-field']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Record Payment', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php
$salesListJson = json_encode($salesList);
$purchasesListJson = json_encode($purchasesList);
$js = <<<JS
const salesList = {$salesListJson};
const purchasesList = {$purchasesListJson};

function updateReferenceOptions() {
    const refType = document.querySelector('select[name="Payments[reference_type]"]').value;
    const refField = document.querySelector('#reference-id-field');
    
    let options = '<option value="">Select document</option>';
    
    if (refType === 'sale') {
        Object.entries(salesList).forEach(([id, label]) => {
            options += `<option value="\${id}">\${label}</option>`;
        });
    } else if (refType === 'purchase') {
        Object.entries(purchasesList).forEach(([id, label]) => {
            options += `<option value="\${id}">\${label}</option>`;
        });
    }
    
    refField.innerHTML = options;
}

document.querySelector('select[name="Payments[reference_type]"]').addEventListener('change', updateReferenceOptions);
window.addEventListener('load', updateReferenceOptions);
JS;

$js = str_replace(
    ['{$salesListJson}', '{$purchasesListJson}'],
    [$salesListJson, $purchasesListJson],
    $js
);

$this->registerJs($js);
?>
