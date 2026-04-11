<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var float $totalRevenue */
/** @var float $totalCogs */
/** @var float $grossProfit */
/** @var float $grossMargin */

$this->title = 'Profit & Loss Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$profitColor = $grossProfit > 0 ? 'green' : ($grossProfit < 0 ? 'red' : 'gray');
?>
<div class="report-profit-loss">
    <p>Basic financial overview - Revenue vs Cost of Goods Sold.</p>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Revenue Section</h5>
                    <table class="table table-sm">
                        <tr>
                            <th>Total Sales Revenue:</th>
                            <td style="text-align: right; font-weight: bold; color: green;">
                                <?= Yii::$app->formatter->asDecimal($totalRevenue, 2) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Cost of Goods Sold (COGS):</th>
                            <td style="text-align: right; font-weight: bold; color: red;">
                                (<?= Yii::$app->formatter->asDecimal($totalCogs, 2) ?>)
                            </td>
                        </tr>
                        <tr style="border-top: 2px solid #ddd;">
                            <th>Gross Profit:</th>
                            <td style="text-align: right; font-weight: bold; color: <?= $profitColor ?>; font-size: 1.2em;">
                                <?= Yii::$app->formatter->asDecimal($grossProfit, 2) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Gross Profit Margin:</th>
                            <td style="text-align: right; font-weight: bold;">
                                <?= number_format($grossMargin, 2) ?>%
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
