<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var array $payments */
/** @var float $remainingBalance */

$this->title = 'Sale #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-view">

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Sale Information</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%;">Customer:</th>
                            <td><?= $model->customer ? Html::a($model->customer->name, ['party/view', 'id' => $model->customer_id]) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Sale Date:</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->sale_date, 'php:Y-m-d H:i') ?></td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:Y-m-d H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Payment Status</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%;">Total Amount:</th>
                            <td style="font-size: 1.2em; font-weight: bold;"><?= Yii::$app->formatter->asDecimal($model->total_amount, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td><?= Yii::$app->formatter->asDecimal($model->paid_amount, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Outstanding:</th>
                            <td style="font-weight: bold; color: <?= $remainingBalance > 0 ? 'red' : 'green' ?>;">
                                <?= Yii::$app->formatter->asDecimal($remainingBalance, 2) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php
                                $statusMap = [
                                    'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                                    'partial' => '<span class="badge bg-info text-white">Partial</span>',
                                    'paid' => '<span class="badge bg-success text-white">Paid</span>',
                                ];
                                echo $statusMap[$model->status] ?? $model->status;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4>Sale Items</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->saleItems as $item): ?>
                            <tr>
                                <td><?= $item->product ? $item->product->name : 'N/A' ?></td>
                                <td><?= $item->productUnit ? $item->productUnit->unit_name : 'N/A' ?></td>
                                <td><?= $item->quantity ?></td>
                                <td><?= Yii::$app->formatter->asDecimal($item->price, 2) ?></td>
                                <td><?= Yii::$app->formatter->asDecimal($item->total, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($payments)): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <h4>Payment History</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Running Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $runningTotal = 0;
                        foreach ($payments as $payment):
                            $runningTotal += (float)$payment->amount;
                        ?>
                            <tr>
                                <td><?= Yii::$app->formatter->asDatetime($payment->created_at, 'php:Y-m-d H:i') ?></td>
                                <td><?= Yii::$app->formatter->asDecimal($payment->amount, 2) ?></td>
                                <td><?= ucfirst($payment->method) ?></td>
                                <td><?= Yii::$app->formatter->asDecimal($runningTotal, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group mt-3">
        <?php if ($remainingBalance > 0): ?>
            <?= Html::button('Record Payment', ['class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#paymentModal']) ?>
        <?php endif; ?>
        <?= Html::a('Back to Sales', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

</div>

<?php if ($remainingBalance > 0): ?>
<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quickPaymentForm">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" class="form-control" min="0.01" step="0.01" 
                               value="<?= number_format($remainingBalance, 2, '.', '') ?>" required>
                        <small class="form-text text-muted">Max: <?= Yii::$app->formatter->asDecimal($remainingBalance, 2) ?></small>
                    </div>
                    <div class="form-group">
                        <label for="method">Payment Method</label>
                        <select id="method" class="form-control" required>
                            <option value="">Select method</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>
                </form>
                <div id="paymentMessage" class="alert alert-danger d-none mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitPaymentBtn">Record Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('submitPaymentBtn').addEventListener('click', function() {
    const amount = parseFloat(document.getElementById('amount').value);
    const method = document.getElementById('method').value;
    const maxAmount = <?= $remainingBalance ?>;
    const messageDiv = document.getElementById('paymentMessage');

    // Validation
    if (!amount || amount <= 0) {
        messageDiv.textContent = 'Please enter a valid amount.';
        messageDiv.classList.remove('d-none');
        return;
    }

    if (amount > maxAmount) {
        messageDiv.textContent = 'Amount cannot exceed outstanding balance.';
        messageDiv.classList.remove('d-none');
        return;
    }

    if (!method) {
        messageDiv.textContent = 'Please select a payment method.';
        messageDiv.classList.remove('d-none');
        return;
    }

    // Submit via AJAX
    fetch('<?= \yii\helpers\Url::to(['payment/quick-add']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: new URLSearchParams({
            'sale_id': <?= $model->id ?>,
            'amount': amount,
            'method': method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and refresh page to show updated payment history
            document.querySelector('[data-dismiss="modal"].close').click();
            setTimeout(() => window.location.reload(), 300);
        } else {
            messageDiv.textContent = data.message || 'Error recording payment.';
            messageDiv.classList.remove('d-none');
        }
    })
    .catch(error => {
        messageDiv.textContent = 'Error: ' + error.message;
        messageDiv.classList.remove('d-none');
    });
});
</script>
<?php endif; ?>
