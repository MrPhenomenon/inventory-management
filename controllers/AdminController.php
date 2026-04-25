<?php

namespace app\controllers;

use app\components\AppController;
use app\models\Tenants;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class AdminController extends AppController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'adminOnly' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->role === Users::ROLE_ADMIN;
                        },
                    ],
                ],
                'denyCallback' => function () {
                    return $this->redirect(['/']);
                },
            ],
        ]);
    }

    public function actionIndex()
    {
        $tenants = Tenants::find()->with('users')->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', [
            'tenants' => $tenants,
        ]);
    }

    public function actionCreateTenant()
    {
        $model = new Tenants();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Tenant "' . $model->name . '" created successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create-tenant', [
            'model' => $model,
        ]);
    }

    public function actionUsers($tenant_id)
    {
        $tenant = Tenants::findOne($tenant_id);
        if (!$tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        $users = Users::find()->where(['tenant_id' => $tenant_id])->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('users', [
            'tenant' => $tenant,
            'users'  => $users,
        ]);
    }

    public function actionCreateUser()
    {
        $model = new Users();
        $model->role = Users::ROLE_USER;

        $tenants = Tenants::find()->where(['status' => Tenants::STATUS_ACTIVE])->orderBy(['name' => SORT_ASC])->all();

        if ($model->load(Yii::$app->request->post())) {
            $plainPassword = Yii::$app->request->post('Users')['plain_password'] ?? '';

            if (empty($plainPassword)) {
                $model->addError('plain_password', 'Password is required.');
            } else {
                $model->setPassword($plainPassword);
                $model->generateAuthKey();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User "' . $model->name . '" created successfully.');
                    return $this->redirect(['users', 'tenant_id' => $model->tenant_id]);
                }
            }
        }

        return $this->render('create-user', [
            'model'   => $model,
            'tenants' => $tenants,
        ]);
    }

    public function actionToggleUser($id)
    {
        $user = Users::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $user->status = $user->status ? 0 : 1;
        $user->save(false);

        Yii::$app->session->setFlash('success', 'User status updated.');
        return $this->redirect(['users', 'tenant_id' => $user->tenant_id]);
    }

    public function actionToggleTenant($id)
    {
        $tenant = Tenants::findOne($id);
        if (!$tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        $tenant->status = $tenant->status === Tenants::STATUS_ACTIVE
            ? Tenants::STATUS_INACTIVE
            : Tenants::STATUS_ACTIVE;
        $tenant->save(false);

        Yii::$app->session->setFlash('success', 'Tenant status updated.');
        return $this->redirect(['index']);
    }
}
