<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $partyList */
/** @var int $partyId */
/** @var string $type */
/** @var string $dateFrom */
/** @var string $dateTo */

$this->title = 'Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-index">

    <p>
        <?= Html::a('Record Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Filters</h5></div>
        <div class="card-body">
            <form method="get" class="form-inline">
                <div class="form-group mr-3">
                    <label for="party_id" class="mr-2">Party:</label>
                    <select id="party_id" name="party_id" class="form-control">
                        <option value="">All Parties</option>
                        <?php foreach ($partyList as $id => $name): ?>
                            <option value="<?= $id ?>" <?= $partyId == $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label for="type" class="mr-2">Type:</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">All</option>
                        <option value="incoming" <?= $type === 'incoming' ? 'selected' : '' ?>>Incoming</option>
                        <option value="outgoing" <?= $type === 'outgoing' ? 'selected' : '' ?>>Outgoing</option>
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label for="date_from" class="mr-2">From:</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="<?= Html::encode($dateFrom) ?>">
                </div>
                <div class="form-group mr-3">
                    <label for="date_to" class="mr-2">To:</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="<?= Html::encode($dateTo) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'party_id',
                'label' => 'Party',
                'value' => function ($model) {
                    return $model->party ? Html::a($model->party->name, ['party/view', 'id' => $model->party_id]) : 'N/A';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'type',
                'label' => 'Type',
                'value' => function ($model) {
                    $typeMap = [
                        'incoming' => '<span class="badge bg-success">Incoming</span>',
                        'outgoing' => '<span class="badge bg-warning">Outgoing</span>',
                    ];
                    return isset($typeMap[$model->type]) ? $typeMap[$model->type] : $model->type;
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'amount',
                'label' => 'Amount',
                'format' => ['decimal', 2],
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            'method',
            [
                'attribute' => 'reference_type',
                'label' => 'Reference',
                'value' => function ($model) {
                    if ($model->reference_type && $model->reference_id) {
                        if ($model->reference_type === 'sale') {
                            return Html::a("Sale #{$model->reference_id}", ['sales/view', 'id' => $model->reference_id]);
                        } elseif ($model->reference_type === 'purchase') {
                            return Html::a("Purchase #{$model->reference_id}", ['purchase/view', 'id' => $model->reference_id]);
                        }
                    }
                    return '-';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Date',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
