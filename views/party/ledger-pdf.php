<?php
/** @var app\models\Parties $model */
/** @var string $companyName */
/** @var float $totalSales */
/** @var float $totalPurchases */
/** @var float $totalIncomingPayments */
/** @var float $totalOutgoingPayments */
/** @var float $customerBalance */
/** @var float $supplierBalance */
/** @var float $openingBalance */
/** @var string|null $openingBalanceType */
/** @var array $transactions */

$fmt = Yii::$app->formatter;

$companyName = isset($companyName) ? trim((string)$companyName) : '';
$headerName  = $companyName !== '' ? $companyName : $model->name;

$partyLabel = $model->isTypeBoth()
    ? 'Customer / Supplier'
    : ($model->isTypeSupplier() ? 'Supplier' : 'Customer');

$footerDebit  = $totalSales + $totalOutgoingPayments
    + ($openingBalanceType === 'receivable' ? $openingBalance : 0);
$footerCredit = $totalPurchases + $totalIncomingPayments
    + ($openingBalanceType === 'payable' ? $openingBalance : 0);
$footerNet    = $footerDebit - $footerCredit;
?>
<div class="pdf-header">
    <div class="company-name"><?= htmlspecialchars($headerName) ?></div>
    <div class="doc-title">Ledger Statement</div>
</div>

<table class="party-info">
    <tr>
        <td><strong><?= htmlspecialchars($partyLabel) ?>:</strong> <?= htmlspecialchars($model->name) ?></td>
        <td class="text-right"><strong>Statement Date:</strong> <?= date('d M Y') ?></td>
    </tr>
    <tr>
        <td>
            <?php if ($model->phone): ?>Phone: <?= htmlspecialchars($model->phone) ?><?php endif; ?>
            <?php if ($model->address): ?><?= $model->phone ? ' &nbsp;|&nbsp; ' : '' ?>Address: <?= htmlspecialchars($model->address) ?><?php endif; ?>
        </td>
        <td class="text-right">Type: <?= htmlspecialchars($model->displayType()) ?></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:10%;">Date</th>
            <th style="width:12%;">Type</th>
            <th>Description</th>
            <th class="text-right" style="width:12%;">Debit</th>
            <th class="text-right" style="width:12%;">Credit</th>
            <th class="text-right" style="width:12%;">Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $runningBalance = 0;
        foreach ($transactions as $tx):
            $runningBalance += $tx['debit'] - $tx['credit'];
            $isPayment = isset($tx['sort_order']) && $tx['sort_order'] === 1;

            $typeClass = match(true) {
                $tx['type'] === 'Opening Balance'       => 'type-opening',
                str_contains($tx['type'], 'Payment')   => 'type-payment',
                $tx['type'] === 'Sale'                  => 'type-sale',
                default                                 => 'type-purchase',
            };
        ?>
        <tr>
            <td><?= date('d M Y', strtotime($tx['date'])) ?></td>
            <td class="<?= $typeClass ?>"><?= htmlspecialchars($tx['type']) ?></td>
            <td class="<?= $isPayment ? 'indent' : '' ?>"><?= htmlspecialchars($tx['description']) ?></td>
            <td class="text-right"><?= $tx['debit'] > 0 ? number_format($tx['debit']) : '-' ?></td>
            <td class="text-right"><?= $tx['credit'] > 0 ? number_format($tx['credit']) : '-' ?></td>
            <td class="text-right"><?= number_format($runningBalance) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="tfoot">
            <td colspan="3"><strong>Final Balance</strong></td>
            <td class="text-right"><?= number_format($footerDebit) ?></td>
            <td class="text-right"><?= number_format($footerCredit) ?></td>
            <td class="text-right" style="color:<?= $footerNet > 0 ? '#c0392b' : '#27ae60' ?>;">
                <?= number_format($footerNet) ?>
            </td>
        </tr>
    </tfoot>
</table>
