<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Tenants[] $tenants */

$this->title = 'Admin Panel — Tenants';
$this->params['breadcrumbs'][] = 'Admin';
?>
<div class="admin-index">

    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Add Tenant', ['create-tenant'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php if (empty($tenants)): ?>
        <div class="alert alert-info">No tenants yet. Add the first one above.</div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($tenants as $tenant): ?>
        <div class="col-md-6 mb-3">
            <div class="card <?= $tenant->status === 'inactive' ? 'border-secondary' : '' ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <?= Html::encode($tenant->name) ?>
                        <span class="badge <?= $tenant->status === 'active' ? 'badge-success' : 'badge-secondary' ?> ml-2">
                            <?= ucfirst($tenant->status) ?>
                        </span>
                    </h6>
                    <div>
                        <?= Html::a('Users (' . count($tenant->users) . ')', ['users', 'tenant_id' => $tenant->id], ['class' => 'btn btn-info btn-sm']) ?>
                        <?= Html::a(
                            $tenant->status === 'active' ? 'Deactivate' : 'Activate',
                            ['toggle-tenant', 'id' => $tenant->id],
                            ['class' => 'btn btn-sm ' . ($tenant->status === 'active' ? 'btn-warning' : 'btn-success')]
                        ) ?>
                    </div>
                </div>
                <div class="card-body py-2">
                    <small class="text-muted"><?= Html::encode($tenant->email) ?></small><br>
                    <small class="text-muted">Created: <?= Yii::$app->formatter->asDate($tenant->created_at, 'php:Y-m-d') ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
