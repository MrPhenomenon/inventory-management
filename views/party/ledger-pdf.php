<?php
/** @var app\models\Parties $model */
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

$footerDebit  = $totalSales + $totalOutgoingPayments
    + ($openingBalanceType === 'receivable' ? $openingBalance : 0);
$footerCredit = $totalPurchases + $totalIncomingPayments
    + ($openingBalanceType === 'payable' ? $openingBalance : 0);
$footerNet    = $footerDebit - $footerCredit;
?>
<h2><?= htmlspecialchars($model->name) ?></h2>
<div class="meta">
    <?php if ($model->phone): ?>Phone: <?= htmlspecialchars($model->phone) ?> &nbsp;|&nbsp; <?php endif; ?>
    <?php if ($model->address): ?>Address: <?= htmlspecialchars($model->address) ?> &nbsp;|&nbsp; <?php endif; ?>
    Type: <?= htmlspecialchars($model->displayType()) ?>
</div>

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
