<?php

namespace app\components;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Base controller that enforces authentication on all actions.
 * All app controllers extend this instead of yii\web\Controller.
 */
class AppController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Shortcut for the current user's tenant ID.
     */
    protected function getTenantId()
    {
        return Yii::$app->user->identity->tenant_id;
    }
}
