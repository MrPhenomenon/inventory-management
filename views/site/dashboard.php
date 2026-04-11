<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Business Overview';

$salesPercent = $totalSales > 0 ? round(($totalSalesPaid / $totalSales) * 100) : 0;
$purchasePercent = $totalPurchases > 0 ? round(($totalPurchasesPaid / $totalPurchases) * 100) : 0;
?>

<div class="site-dashboard bg-light p-3">

    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.75rem;">Total Sales</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= Yii::$app->formatter->asCurrency($totalSales) ?></div>
                            <small class="text-muted"><?= $salesCount ?> Transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 0.75rem;">Receivables</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= Yii::$app->formatter->asCurrency($totalSalesOutstanding) ?></div>
                            <small class="text-muted"><?= $pendingSalesCount ?> Invoices Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-info border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.75rem;">Total Purchases</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= Yii::$app->formatter->asCurrency($totalPurchases) ?></div>
                            <small class="text-muted"><?= $purchasesCount ?> Transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.75rem;">Payables</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= Yii::$app->formatter->asCurrency($totalPurchasesOutstanding) ?></div>
                            <small class="text-muted"><?= $pendingPurchasesCount ?> Bills Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Collection Status</h6>
                    <?= Html::a('View Details', ['sales/index'], ['class' => 'btn btn-sm btn-outline-primary border-0']) ?>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Collection Rate</small>
                            <span class="h4"><?= $salesPercent ?>%</span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Paid Amount</small>
                            <span class="h4 text-success"><?= Yii::$app->formatter->asCurrency($totalSalesPaid) ?></span>
                        </div>
                    </div>
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $salesPercent ?>%"></div>
                    </div>
                    <div class="row text-center border-top pt-3">
                        <div class="col-4 border-end">
                            <div class="text-muted small">Total</div>
                            <div class="font-weight-bold"><?= $salesCount ?></div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="text-muted small text-success">Paid</div>
                            <div class="font-weight-bold"><?= $paidSalesCount ?></div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small text-warning">Pending</div>
                            <div class="font-weight-bold"><?= $pendingSalesCount ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Purchase Payment Status</h6>
                    <?= Html::a('View Details', ['purchases/index'], ['class' => 'btn btn-sm btn-outline-info border-0']) ?>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Settlement Rate</small>
                            <span class="h4"><?= $purchasePercent ?>%</span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Paid to Suppliers</small>
                            <span class="h4 text-info"><?= Yii::$app->formatter->asCurrency($totalPurchasesPaid) ?></span>
                        </div>
                    </div>
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $purchasePercent ?>%"></div>
                    </div>
                    <div class="row text-center border-top pt-3">
                        <div class="col-4 border-end">
                            <div class="text-muted small">Total</div>
                            <div class="font-weight-bold"><?= $purchasesCount ?></div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="text-muted small text-success">Paid</div>
                            <div class="font-weight-bold"><?= $paidPurchasesCount ?></div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small text-warning">Pending</div>
                            <div class="font-weight-bold"><?= $pendingPurchasesCount ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Cash Flow Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around align-items-center py-3">
                        <div class="text-center">
                            <div class="text-success h3 mb-0"><?= Yii::$app->formatter->asCurrency($totalIncomingPayments) ?></div>
                            <div class="small text-muted text-uppercase">Total Inflow</div>
                        </div>
                        <div class="border-start h-100" style="width:1px; min-height: 50px;"></div>
                        <div class="text-center">
                            <div class="text-danger h3 mb-0"><?= Yii::$app->formatter->asCurrency($totalOutgoingPayments) ?></div>
                            <div class="small text-muted text-uppercase">Total Outflow</div>
                        </div>
                    </div>
                    <div class="alert alert-secondary border-0 mt-3 d-flex justify-content-between align-items-center">
                        <span>Net Movement: <strong><?= Yii::$app->formatter->asCurrency($totalPayments) ?></strong></span>
                        <?= Html::a('Payment Ledger', ['payment/index'], ['class' => 'btn btn-sm btn-dark']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 text-white">
                    <h6 class="m-0 font-weight-bold text-dark">Inventory Overview</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center mb-3">
                        <div class="display-4 me-3 text-primary"><?= $productsCount ?></div>
                        <div>
                            <div class="fw-bold">Active Products</div>
                            <div class="text-muted small">In your catalog</div>
                        </div>
                    </div>
                    <div class="btn-group w-100">
                        <?= Html::a('Stock Levels', ['inventory/index'], ['class' => 'btn btn-outline-primary']) ?>
                        <?= Html::a('Manage Products', ['product/index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">Latest Sales</h6>
                </div>
                <div class="list-group list-group-flush shadow-none">
                    <?php if (!empty($recentSales)): ?>
                        <?php foreach ($recentSales as $sale): ?>
                            <a href="<?= Url::to(['sales/view', 'id' => $sale->id]) ?>" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold">#<?= $sale->id ?></h6>
                                    <span class="text-primary fw-bold"><?= Yii::$app->formatter->asCurrency($sale->total_amount) ?></span>
                                </div>
                                <small class="text-muted d-block"><?= $sale->customer ? $sale->customer->name : 'Walk-in Customer' ?></small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted small">No recent sales recorded.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-info">Latest Purchases</h6>
                </div>
                <div class="list-group list-group-flush shadow-none">
                    <?php if (!empty($recentPurchases)): ?>
                        <?php foreach ($recentPurchases as $purchase): ?>
                            <a href="<?= Url::to(['purchase/view', 'id' => $purchase->id]) ?>" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold">#<?= $purchase->id ?></h6>
                                    <span class="text-info fw-bold"><?= Yii::$app->formatter->asCurrency($purchase->total_amount) ?></span>
                                </div>
                                <small class="text-muted d-block"><?= $purchase->supplier ? $purchase->supplier->name : 'N/A' ?></small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted small">No recent purchases recorded.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-success">Latest Payments</h6>
                </div>
                <div class="list-group list-group-flush shadow-none">
                    <?php if (!empty($recentPayments)): ?>
                        <?php foreach ($recentPayments as $payment): ?>
                            <div class="list-group-item py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <small class="text-uppercase text-muted d-block" style="font-size: 0.65rem;">
                                            <?= $payment->reference_type ?>
                                        </small>
                                        <h6 class="mb-0 fw-bold"><?= $payment->party ? $payment->party->name : 'N/A' ?></h6>
                                    </div>
                                    <span class="badge rounded-pill <?= $payment->type === 'incoming' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= Yii::$app->formatter->asCurrency($payment->amount) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted small">No recent payments recorded.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>