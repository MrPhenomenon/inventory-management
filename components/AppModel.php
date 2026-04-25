<?php

namespace app\components;

use Yii;

/**
 * Base model that auto-scopes all queries and saves to the current tenant.
 * Scoping is handled by TenantQuery which applies tenant_id at SQL build time,
 * so it survives any subsequent where() calls from controllers.
 */
class AppModel extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return Yii::createObject(TenantQuery::class, [static::class]);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert && !Yii::$app->user->isGuest) {
            $identity = Yii::$app->user->identity;
            if ($identity && $identity->role !== 'admin') {
                $this->tenant_id = $identity->tenant_id;
            }
        }

        return true;
    }
}
