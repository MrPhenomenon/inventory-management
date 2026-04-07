<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProductUnits $model */
/** @var app\models\Products $product */

$this->title = 'Add Unit for: ' . $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['/product/index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['/productunit/index', 'product_id' => $product->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-units-create">

    <div style="margin-bottom: 20px; padding: 10px; background-color: #e8f4f8; border-radius: 4px;">
        <p>
            <strong>Product:</strong> <?= $product->name ?><br>
            <strong>Base Unit:</strong> <?= $product->baseUnit ? $product->baseUnit->name : 'N/A' ?>
        </p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'product' => $product,
    ]) ?>

</div>
