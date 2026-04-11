<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Products;
use app\models\ProductUnits;

/** @var yii\web\View $this */
/** @var app\models\InventoryTransactions $model */

$this->title = 'Adjust Stock';
$this->params['breadcrumbs'][] = ['label' => 'Inventory', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$products = Products::find()->asArray()->all();
$productList = array_combine(
    array_column($products, 'id'),
    array_column($products, 'name')
);
?>
<div class="inventory-adjust">

    <div class="adjustment-form">
        <?php $form = ActiveForm::begin(['id' => 'adjustment-form']); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'product_id')->dropDownList($productList, ['prompt' => 'Select a product']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'product_unit_id')->dropDownList([], ['prompt' => 'Select a unit', 'disabled' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '0.001', 'placeholder' => '0.00']) ?>
            </div>
        </div>

        <div class="form-group">
            <label>Note:</label>
            <p class="text-muted">
                Enter the quantity to adjust. Use positive numbers to add stock, negative numbers to deduct stock.
            </p>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Adjust Stock', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php
$ajaxUrlUnits = Url::to(['/productunit/list']);
$js = <<<JS
const ajaxUrlUnits = '$ajaxUrlUnits';

function loadUnits(productId) {
    const unitSelect = document.querySelector('select[name="InventoryTransactions[product_unit_id]"]');
    
    if (!productId) {
        unitSelect.innerHTML = '<option value="">Select a unit</option>';
        unitSelect.disabled = true;
        return;
    }
    
    unitSelect.disabled = true;
    unitSelect.innerHTML = '<option value="">Loading...</option>';
    
    fetch(ajaxUrlUnits + '?product_id=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                let options = '<option value="">Select a unit</option>';
                data.data.forEach(unit => {
                    options += `<option value="\${unit.id}">\${unit.unit_name}</option>`;
                });
                unitSelect.innerHTML = options;
                unitSelect.disabled = false;
            } else {
                unitSelect.innerHTML = '<option value="">No units available</option>';
                unitSelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
        });
}

document.querySelector('select[name="InventoryTransactions[product_id]"]').addEventListener('change', function() {
    loadUnits(this.value);
});
JS;

$this->registerJs($js);
?>
