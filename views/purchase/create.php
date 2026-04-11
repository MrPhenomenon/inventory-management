<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Parties;
use app\models\Products;
$this->title = 'Create Purchase';
$this->params['breadcrumbs'][] = ['label' => 'Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$suppliers = Parties::find()
    ->where(['in', 'type', ['supplier', 'both']])
    ->asArray()
    ->all();
$supplierList = array_combine(
    array_column($suppliers, 'id'),
    array_column($suppliers, 'name')
);

$products = Products::find()->asArray()->all();
$productList = array_combine(
    array_column($products, 'id'),
    array_column($products, 'name')
);
?>
<div class="purchases-create">

    <div class="purchase-form">
        <?php $form = ActiveForm::begin(['id' => 'purchase-form']); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'supplier_id')->dropDownList($supplierList, ['prompt' => 'Select a supplier']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'purchase_date')->textInput(['type' => 'datetime-local', 'value' => date('Y-m-d\TH:i', strtotime($model->purchase_date ?: 'now'))]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'paid_amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0, 'id' => 'paid-amount']) ?>
                <small class="form-text text-muted">If filled, a payment record will be created</small>
            </div>
            <div class="col-md-6" id="paymentMethodField" style="display: none;">
                <div class="form-group">
                    <label for="paymentMethod">Payment Method <span style="color: red;">*</span></label>
                    <select id="paymentMethod" class="form-control" name="payment_method">
                        <option value="">Select method</option>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                    </select>
                    <small class="form-text text-muted">Required when amount is entered</small>
                </div>
            </div>
        </div>

        <hr>
        <h4>Purchase Items</h4>

        <div class="table-responsive">
            <table class="table table-bordered" id="purchase-items-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Product</th>
                        <th style="width: 20%;">Unit</th>
                        <th style="width: 15%;">Quantity</th>
                        <th style="width: 15%;">Price</th>
                        <th style="width: 15%;">Total</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody id="items-container">
                    <!-- Items will be added here -->
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-secondary mb-3" id="add-item-btn">Add Item</button>

        <div class="form-group">
            <label for="grand-total">Grand Total:</label>
            <input type="text" id="grand-total" class="form-control" name="total_amount" readonly
                style="font-weight: bold; font-size: 1.1em;">
        </div>

        <div class="form-group mt-3">
            <?= Html::submitButton('Create Purchase', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php

$productListJs = json_encode($productList);
$ajaxUrlUnits = yii\helpers\Url::to(['/productunit/list']);

$js = <<<JS
let itemCount = 0;
const productList = $productListJs;
const ajaxUrlUnits = '$ajaxUrlUnits';

function addItem() {
    itemCount++;
    const html = `
        <tr class="purchase-item-row" data-item-id="\${itemCount}">
            <td>
                <select name="items[\${itemCount}][product_id]" class="form-control product-select">
                    <option value="">Select a product</option>
                    \${Object.entries(productList)
    .map(([id, name]) => `<option value="\${id}">\${name}</option>`)
    .join('')}
                </select>
            </td>
            <td>
                <select name="items[\${itemCount}][product_unit_id]" class="form-control unit-select" disabled>
                    <option value="">Select a unit</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[\${itemCount}][quantity]" class="form-control quantity" step="0.001" min="0" value="1">
            </td>
            <td>
                <input type="number" name="items[\${itemCount}][price]" class="form-control price" step="0.01" min="0" value="0">
            </td>
            <td>
                <input type="text" class="form-control row-total" readonly style="font-weight: bold;">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm">Remove</button>
            </td>
        </tr>
    `;
    document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
}


    function removeItem(btn) {
    btn.closest('tr').remove();
    calculateGrandTotal();
}

function loadUnits(selectElement) {
    const row = selectElement.closest('tr');
    const productId = selectElement.value;
    const unitSelect = row.querySelector('.unit-select');
    
    if (!productId) {
        unitSelect.innerHTML = '<option value="">Select a unit</option>';
        unitSelect.disabled = true;
        calculateGrandTotal();
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
            }
        })
        .catch(error => {
            console.error('Error:', error);
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
        });
}

function calculateRowTotal(element) {
    const row = element.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const total = quantity * price;
    
    row.querySelector('.row-total').value = total.toFixed(2);
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.row-total').forEach(element => {
        grandTotal += parseFloat(element.value) || 0;
    });
    document.getElementById('grand-total').value = grandTotal.toFixed(2);
}

document.getElementById('add-item-btn').addEventListener('click', function() {
    addItem();
});

window.addEventListener('load', function() {
    addItem();
});
document.getElementById('items-container').addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        loadUnits(e.target);
    }
    if (e.target.classList.contains('quantity') || e.target.classList.contains('price')) {
        calculateRowTotal(e.target);
    }
});

document.getElementById('items-container').addEventListener('click', function(e) {
    if (e.target.closest('.btn-danger')) {
        removeItem(e.target);
    }
});

// Show payment method field when paid_amount has a value
const paidAmountField = document.getElementById('paid-amount');
const paymentMethodField = document.getElementById('paymentMethodField');
const paymentMethodSelect = document.getElementById('paymentMethod');

function togglePaymentMethod() {
    const paidAmount = parseFloat(paidAmountField.value) || 0;
    if (paidAmount > 0) {
        paymentMethodField.style.display = 'block';
        paymentMethodSelect.required = true;
    } else {
        paymentMethodField.style.display = 'none';
        paymentMethodSelect.required = false;
        paymentMethodSelect.value = '';
    }
}

paidAmountField.addEventListener('change', togglePaymentMethod);
paidAmountField.addEventListener('input', togglePaymentMethod);

// Validate form submission
document.getElementById('purchase-form').addEventListener('submit', function(e) {
    const paidAmount = parseFloat(paidAmountField.value) || 0;
    if (paidAmount > 0 && !paymentMethodSelect.value) {
        e.preventDefault();
        alert('Payment method is required when payment amount is entered.');
        return false;
    }
});

window.addEventListener('load', function() {
    addItem();
});
JS;

$this->registerJs($js);
?>