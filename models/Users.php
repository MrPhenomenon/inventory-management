<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['name', 'email', 'role'], 'required'],
            [['name', 'email'], 'string', 'max' => 150],
            ['email', 'email'],
            ['email', 'unique'],
            ['role', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_USER]],
            ['status', 'default', 'value' => 1],
            ['tenant_id', 'required', 'when' => function ($model) {
                return $model->role === self::ROLE_USER;
            }, 'message' => 'Tenant is required for regular users.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'tenant_id' => 'Tenant',
            'name'      => 'Name',
            'email'     => 'Email',
            'role'      => 'Role',
            'status'    => 'Status',
        ];
    }

    public function getTenant()
    {
        return $this->hasOne(Tenants::class, ['id' => 'tenant_id']);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function optsRole()
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_USER  => 'User',
        ];
    }

    // --- IdentityInterface ---

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 1]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => 1]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}
