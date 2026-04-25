<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Tenants $tenant */
/** @var app\models\Users[] $users */

$this->title = 'Users — ' . $tenant->name;
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-users">

    <p>
        <?= Html::a(
            '<i class="fas fa-plus"></i> Add User',
            ['create-user', 'tenant_id' => $tenant->id],
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?php if (empty($users)): ?>
        <div class="alert alert-info">No users yet for this tenant.</div>
    <?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= Html::encode($user->name) ?></td>
                        <td><?= Html::encode($user->email) ?></td>
                        <td><span class="badge badge-secondary"><?= Html::encode($user->role) ?></span></td>
                        <td>
                            <span class="badge <?= $user->status ? 'badge-success' : 'badge-danger' ?>">
                                <?= $user->status ? 'Active' : 'Disabled' ?>
                            </span>
                        </td>
                        <td><?= Yii::$app->formatter->asDate($user->created_at, 'php:Y-m-d') ?></td>
                        <td>
                            <?= Html::a(
                                $user->status ? 'Disable' : 'Enable',
                                ['toggle-user', 'id' => $user->id],
                                ['class' => 'btn btn-sm ' . ($user->status ? 'btn-warning' : 'btn-success')]
                            ) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
