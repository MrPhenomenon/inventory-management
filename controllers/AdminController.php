<?php

namespace app\controllers;

use app\components\AppController;
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
        $users = Users::find()->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', [
            'users' => $users,
        ]);
    }

    public function actionCreateUser()
    {
        $model = new Users();
        $model->role = Users::ROLE_USER;

        if ($model->load(Yii::$app->request->post())) {
            $plainPassword = Yii::$app->request->post('Users')['plain_password'] ?? '';

            if (empty($plainPassword)) {
                $model->addError('plain_password', 'Password is required.');
            } else {
                $model->setPassword($plainPassword);
                $model->generateAuthKey();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User "' . $model->name . '" created successfully.');
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create-user', [
            'model' => $model,
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
        return $this->redirect(['index']);
    }
}
