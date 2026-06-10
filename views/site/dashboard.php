<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';

function itemsSummary(array $items): string {
    $parts = [];
    foreach ($items as $item) {
        $name = $item->product ? $item->product->name : '?';
        $unit = $item->productUnit ? $item->productUnit->unit_name : '';
        $qty  = rtrim(rtrim(number_format((float)$item->quantity, 3, '.', ''), '0'), '.');
        $parts[] = $unit ? "{$name} ({$unit} × {$qty})" : "{$name} × {$qty}";
    }
    return implode(', ', $parts) ?: '—';
}

$statusBadge = [
    'paid'    => '<span class="badge badge-success">Paid</span>',
    'partial' => '<span class="badge badge-info">Partial</span>',
    'pending' => '<span class="badge badge-secondary">Pending</span>',
];
?>

<div class="dashboard">

    <!-- ── Row 1: Key Numbers ── -->
    <div class="row mb-4">

        <div class="col-xl-3 col-md-6 mb-3">
            <a href="<?= Url::to(['account/chemical-ledger']) ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-xs text-uppercase text-muted font-weight-bold mb-1">
                            <i class="fas fa-flask mr-1"></i> Chemical Account
                        </div>
                        <div class="h3 mb-0 <?= $chemicalBalance < 0 ? 'text-danger' : '' ?>">
                            <?= number_format($chemicalBalance) ?>
                        </div>
                        <small class="text-muted">purchases − sales − nils</small>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <small class="text-muted">View Ledger →</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <a href="<?= Url::to(['account/index']) ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-xs text-uppercase text-muted font-weight-bold mb-1">
                            <i class="fas fa-university mr-1"></i> Cash &amp; Bank
                        </div>
                        <div class="h3 mb-1"><?= number_format($totalAccountsBalance) ?></div>
                        <?php foreach ($accountRows as $acc): ?>
                            <small class="d-block text-muted">
                                <?= Html::encode($acc['name']) ?>:
                                <span class="<?= $acc['balance'] < 0 ? 'text-danger' : '' ?>"><?= number_format($acc['balance']) ?></span>
                            </small>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <small class="text-muted">View Accounts →</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <a href="<?= Url::to(['report/receivables']) ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-xs text-uppercase text-muted font-weight-bold mb-1">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Receivables
                        </div>
                        <div class="h3 mb-0"><?= number_format($totalReceivables) ?></div>
                        <small class="text-muted"><?= $receivableCount ?> <?= $receivableCount === 1 ? 'customer' : 'customers' ?></small>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <small class="text-muted">View Report →</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <a href="<?= Url::to(['report/payables']) ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-xs text-uppercase text-muted font-weight-bold mb-1">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> Payables
                        </div>
                        <div class="h3 mb-0 <?= $totalPayables > 0 ? 'text-danger' : '' ?>"><?= number_format($totalPayables) ?></div>
                        <small class="text-muted"><?= $payableCount ?> <?= $payableCount === 1 ? 'supplier' : 'suppliers' ?></small>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <small class="text-muted">View Report →</small>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- ── Row 2: Expenses + Position ── -->
    <div class="row mb-4">

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-circle mr-1"></i> Pending Expenses
                    </h6>
                    <?= Html::a('Manage', ['expense/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($expenseRows)): ?>
                        <div class="p-4 text-center text-muted small">
                            <i class="fas fa-check-circle mr-1"></i> All expenses settled.
                        </div>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <?php foreach ($expenseRows as $row): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($row['name']), ['expense/view', 'id' => $row['id']]) ?></td>
                                <td class="text-right"><?= number_format($row['pending']) ?></td>
                                <td class="text-right" style="width:60px;">
                                    <?= Html::a('Nil', ['expense/nil', 'id' => $row['id']], ['class' => 'btn btn-xs btn-outline-secondary']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light font-weight-bold">
                                <td>Total</td>
                                <td class="text-right"><?= number_format($totalPendingExpenses) ?></td>
                                <td></td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <?php
            $side1 = $chemicalBalance + $totalAccountsBalance + $totalReceivables + $totalPendingExpenses;
            $side2 = $totalPayables;
            $net   = $side1 - $side2;
            ?>
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-balance-scale mr-1"></i> Position Snapshot
                    </h6>
                    <?= Html::a('Balance Sheet', ['report/balance-sheet'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted pl-3">Chemical Account</td>
                            <td class="text-right pr-3"><?= number_format($chemicalBalance) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Cash &amp; Bank</td>
                            <td class="text-right pr-3"><?= number_format($totalAccountsBalance) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Receivables</td>
                            <td class="text-right pr-3"><?= number_format($totalReceivables) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Pending Expenses</td>
                            <td class="text-right pr-3"><?= number_format($totalPendingExpenses) ?></td>
                        </tr>
                        <tr class="table-light font-weight-bold">
                            <td class="pl-3">Assets Total</td>
                            <td class="text-right pr-3"><?= number_format($side1) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Payables</td>
                            <td class="text-right pr-3 text-danger">(<?= number_format($side2) ?>)</td>
                        </tr>
                        <tr class="font-weight-bold">
                            <td class="pl-3">Net Position</td>
                            <td class="text-right pr-3 <?= $net < 0 ? 'text-danger' : '' ?>"><?= number_format($net) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Row 3: Recent Activity ── -->
    <div class="row">

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-arrow-up mr-1"></i> Recent Sales
                    </h6>
                    <?= Html::a('All Sales', ['sales/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentSales)): ?>
                        <div class="p-4 text-center text-muted small">No sales yet.</div>
                    <?php else: ?>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:90px;">Date</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th class="text-right">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td class="text-muted small"><?= date('d M Y', strtotime($sale->sale_date)) ?></td>
                                    <td>
                                        <?php if ($sale->customer): ?>
                                            <?= Html::a(Html::encode($sale->customer->name), ['/party/view', 'id' => $sale->customer_id]) ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?= Html::encode(itemsSummary($sale->saleItems)) ?></td>
                                    <td class="text-right"><?= number_format($sale->total_amount) ?></td>
                                    <td><?= $statusBadge[$sale->status] ?? '' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-arrow-down mr-1"></i> Recent Purchases
                    </h6>
                    <?= Html::a('All Purchases', ['purchase/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentPurchases)): ?>
                        <div class="p-4 text-center text-muted small">No purchases yet.</div>
                    <?php else: ?>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:90px;">Date</th>
                                    <th>Supplier</th>
                                    <th>Items</th>
                                    <th class="text-right">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentPurchases as $purchase): ?>
                                <tr>
                                    <td class="text-muted small"><?= date('d M Y', strtotime($purchase->purchase_date)) ?></td>
                                    <td>
                                        <?php if ($purchase->supplier): ?>
                                            <?= Html::a(Html::encode($purchase->supplier->name), ['/party/view', 'id' => $purchase->supplier_id]) ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?= Html::encode(itemsSummary($purchase->purchaseItems)) ?></td>
                                    <td class="text-right"><?= number_format($purchase->total_amount) ?></td>
                                    <td><?= $statusBadge[$purchase->status] ?? '' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>
