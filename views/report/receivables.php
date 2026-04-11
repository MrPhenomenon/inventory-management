<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */
/** @var float $totalReceivables */

$this->title = 'Receivables Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-receivables">

    <p>Outstanding balances from customers.</p>

    <div class="alert alert-info">
        <strong>Total Outstanding Receivables:</strong>
        <span style="font-size: 1.5em; font-weight: bold; color: red;">
            <?= Yii::$app->formatter->asDecimal($totalReceivables, 2) ?>
        </span>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => 'Customer Name',
                'value' => function ($data) {
                    return Html::a($data['name'], ['party/view', 'id' => $data['id']], ['target' => '_blank']);
                },
                'format' => 'html',
            ],
            'phone',
            [
                'attribute' => 'total_sales',
                'label' => 'Total Sales',
                'format' => ['decimal', 2],
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'total_payments',
                'label' => 'Payments Received',
                'format' => ['decimal', 2],
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'balance',
                'label' => 'Outstanding Balance',
                'value' => function ($data) {
                    $balance = $data['balance'];
                    return "<strong style=\"color: red;\">" . Yii::$app->formatter->asDecimal($balance, 2) . "</strong>";
                },
                'format' => 'html',
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
