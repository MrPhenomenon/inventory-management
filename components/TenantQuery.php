<?php

namespace app\components;

use Yii;

/**
 * Custom ActiveQuery that injects tenant_id into every query at build time,
 * AFTER all where() / andWhere() calls from controllers have already been applied.
 * This prevents controllers from accidentally replacing the tenant scope via where().
 */
class TenantQuery extends \yii\db\ActiveQuery
{
    public function prepare($builder)
    {
        if (!Yii::$app->user->isGuest) {
            $identity = Yii::$app->user->identity;
            if ($identity && $identity->role !== 'admin') {
                $modelClass = $this->modelClass;
                $this->andWhere([$modelClass::tableName() . '.tenant_id' => $identity->tenant_id]);
            }
        }

        return parent::prepare($builder);
    }
}
