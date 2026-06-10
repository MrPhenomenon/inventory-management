<?php

use yii\helpers\Html;

/** @var app\models\Users[] $users */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = 'Admin';
?>
<div class="admin-index">

    <p>
        <?= Html::a('<i class="fas fa-user-plus"></i> Add User', ['create-user'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php if (empty($users)): ?>
        <div class="alert alert-info">No users yet.</div>
    <?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= Html::encode($user->name) ?></td>
                    <td><?= Html::encode($user->email) ?></td>
                    <td><span class="badge badge-<?= $user->role === 'admin' ? 'danger' : 'secondary' ?>"><?= ucfirst($user->role) ?></span></td>
                    <td>
                        <span class="badge badge-<?= $user->status ? 'success' : 'warning' ?>">
                            <?= $user->status ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($user->id !== Yii::$app->user->id): ?>
                        <?= Html::a(
                            $user->status ? 'Deactivate' : 'Activate',
                            ['toggle-user', 'id' => $user->id],
                            ['class' => 'btn btn-xs ' . ($user->status ? 'btn-warning' : 'btn-success')]
                        ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
