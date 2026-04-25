<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Stock Levels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-index">

    <div class="mb-3">
        <?= Html::a('Adjust Stock', ['adjust'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('View Transactions', ['transactions'], ['class' => 'btn btn-info']) ?>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
        [
                'attribute' => 'name',
                'label' => 'Product Name',
                'value' => function ($data) {
                    return Html::a($data['name'], ['product/index', 'Products[name]' => $data['name']], ['target' => '_blank']);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'category',
                'label' => 'Category',
                ],
                [
                    'label' => 'Unit Breakdown',
                    'format' => 'html',
                    'value' => function ($data) {
                        if (empty($data['unit_breakdown'])) {
                            return '<span class="text-muted" style="font-size:0.8em;">—</span>';
                        }
                        $lines = [];
                        foreach ($data['unit_breakdown'] as $u) {
                            $qty = $u['qty'];
                            $formatted = ($qty == floor($qty))
                                ? number_format($qty, 0)
                                : number_format($qty, 2);
                            $color = $qty > 0 ? '#155724' : ($qty < 0 ? '#721c24' : '#6c757d');
                            $lines[] = '<span style="color:' . $color . '; white-space:nowrap;">'
                                . htmlspecialchars($u['name']) . ': <strong>' . $formatted . '</strong></span>';
                        }
                        return '<div style="font-size:0.8em; line-height:1.7;">' . implode('<br>', $lines) . '</div>';
                    },
                ],
            [
                'attribute' => 'base_stock',
                'label' => 'Current Stock (Base Unit)',
                'value' => function ($data) {
                    $stock = $data['base_stock'];
                    $color = $stock > 0 ? 'green' : ($stock < 0 ? 'red' : 'gray');
                    return "<span style=\"color: {$color}; font-weight: bold;\">" . number_format($stock, 2) . "</span>";
                },
                'format' => 'html',
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'base_unit',
                'label' => 'Base Unit',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $data) {
                        return Html::a('View Details', ['inventory/transactions', 'product_id' => $data['id']], ['class' => 'btn btn-info btn-sm', 'data-pjax' => 0]);
                    },
                ],
                'urlCreator' => function ($action, $data, $key, $index) {
                    return '#';
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
