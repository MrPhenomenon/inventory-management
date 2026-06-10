<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Parties;
use app\models\Products;

// Resolve Cash Sale party ID dynamically
$cashSaleParty = Parties::find()->where(['name' => 'Cash Sale'])->one();
$cashSaleId    = $cashSaleParty ? (int)$cashSaleParty->id : 0;

$this->title = 'Create Sale';
$this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$customers = Parties::find()
    ->where(['in', 'type', [Parties::TYPE_CUSTOMER, Parties::TYPE_BOTH]])
    ->asArray()
    ->all();
$customerList = array_combine(
    array_column($customers, 'id'),
    array_column($customers, 'name')
);

$products = Products::find()->asArray()->all();
$productList = array_combine(
    array_column($products, 'id'),
    array_column($products, 'name')
);
?>
<div class="sales-create">

    <div class="sale-form">
        <?php $form = ActiveForm::begin(['id' => 'sale-form']); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'customer_id')->dropDownList($customerList, ['prompt' => 'Select a customer']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sale_date')->textInput(['type' => 'datetime-local', 'value' => date('Y-m-d\TH:i', strtotime($model->sale_date ?: 'now'))]) ?>
            </div>
        </div>

        <hr>
        <h4>Sale Items</h4>

        <div class="table-responsive">
            <table class="table table-bordered" id="sale-items-table">
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
            <input type="text" id="grand-total" class="form-control" name="Sales[total_amount]" readonly
                style="font-weight: bold; font-size: 1.1em;" value="<?= isset($model->total_amount) ? number_format($model->total_amount, 2, '.', '') : '0.00' ?>">
        </div>

        <div class="form-group mt-3">
            <?= Html::submitButton('Create Sale', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php
$productListJs = json_encode($productList);
$ajaxUrlUnits = Url::to(['/productunit/list']);
$itemsData = json_encode($items ?: []);
$js = <<<JS
let itemCount = 0;
const productList = $productListJs;
const ajaxUrlUnits = '$ajaxUrlUnits';
const existingItems = '$itemsData';

function addItem(item = null) {
    itemCount++;
    const productId = item ? item.product_id : '';
    const unitId = item ? item.product_unit_id : '';
    const quantity = item ? item.quantity : 1;
    const price = item ? item.price : 0;
    const total = item ? item.total : 0;

    const html = `
        <tr class="sale-item-row" data-item-id="\${itemCount}">
            <td>
                <select name="items[\${itemCount}][product_id]" class="form-control product-select">
                    <option value="">Select a product</option>
                    \${Object.entries(productList)
    .map(([id, name]) => `<option value="\${id}" \${productId == id ? 'selected' : ''}>\${name}</option>`)
    .join('')}
                </select>
            </td>
            <td>
                <select name="items[\${itemCount}][product_unit_id]" class="form-control unit-select" \${productId ? '' : 'disabled'}>
                    <option value="">Select a unit</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[\${itemCount}][quantity]" class="form-control quantity" step="0.001" min="0" value="\${quantity}">
            </td>
            <td>
                <input type="number" name="items[\${itemCount}][price]" class="form-control price" step="0.01" min="0" value="\${price}">
            </td>
            <td>
                <input type="number" name="items[\${itemCount}][total]" class="form-control row-total total" step="0.01" min="0" value="\${parseFloat(total).toFixed(2)}" style="font-weight: bold;">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm">Remove</button>
            </td>
        </tr>
    `;

    document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
    const row = document.querySelector(`#items-container tr[data-item-id="\${itemCount}"]`);
    const productSelect = row.querySelector('.product-select');

    if (productId) {
        loadUnits(productSelect, unitId);
    }
}

function removeItem(button) {
    button.closest('tr').remove();
    calculateGrandTotal();
}

function loadUnits(selectElement, selectedUnitId = '') {
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
                    options += `<option value="\${unit.id}" \${selectedUnitId == unit.id ? 'selected' : ''}>\${unit.unit_name}</option>`;
                });
                unitSelect.innerHTML = options;
                unitSelect.disabled = false;
            } else {
                unitSelect.innerHTML = '<option value="">No units available</option>';
                unitSelect.disabled = false;
            }
        })
        .catch(() => {
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
            unitSelect.disabled = false;
        });
}

function calculateRowValues(element, source) {
    const row = element.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const priceInput = row.querySelector('.price');
    const totalInput = row.querySelector('.row-total');
    let price = parseFloat(priceInput.value) || 0;
    let total = parseFloat(totalInput.value) || 0;
    const computedTotal = quantity * price;

    if (source === 'quantity') {
        if (quantity > 0 && Math.abs(total - computedTotal) > 0.0001 && total > 0) {
            price = total / quantity;
            priceInput.value = price.toFixed(2);
        } else {
            total = computedTotal;
            totalInput.value = total.toFixed(2);
        }
    } else if (source === 'price') {
        total = quantity * price;
        totalInput.value = total.toFixed(2);
    } else if (source === 'total') {
        if (quantity > 0) {
            price = total / quantity;
        } else {
            price = 0;
        }
        priceInput.value = price.toFixed(2);
    }

    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.row-total').forEach(element => {
        grandTotal += parseFloat(element.value) || 0;
    });
    document.getElementById('grand-total').value = grandTotal.toFixed(2);
}

function initializeItems() {
    if (existingItems.length > 0) {
        existingItems.forEach(item => addItem(item));
    } else {
        addItem();
    }
    calculateGrandTotal();
}

document.getElementById('add-item-btn').addEventListener('click', () => addItem());

document.getElementById('items-container').addEventListener('change', event => {
    if (event.target.classList.contains('product-select')) {
        loadUnits(event.target);
    }
    if (event.target.classList.contains('quantity') || event.target.classList.contains('price') || event.target.classList.contains('total')) {
        const source = event.target.classList.contains('quantity') ? 'quantity'
            : event.target.classList.contains('price') ? 'price'
            : 'total';
        calculateRowValues(event.target, source);
    }
});

document.getElementById('items-container').addEventListener('click', event => {
    if (event.target.closest('.btn-danger')) {
        removeItem(event.target);
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
document.getElementById('sale-form').addEventListener('submit', function(e) {
    const paidAmount = parseFloat(paidAmountField.value) || 0;
    if (paidAmount > 0 && !paymentMethodSelect.value) {
        e.preventDefault();
        alert('Payment method is required when payment amount is entered.');
        return false;
    }
});

window.addEventListener('load', initializeItems);
JS;

$this->registerJs($js);
?>
