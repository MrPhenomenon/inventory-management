<?php

namespace app\models;

use Yii;

class Tenants extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return 'tenants';
    }

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email'], 'string', 'max' => 150],
            ['email', 'email'],
            ['email', 'unique'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Business Name',
            'email'      => 'Email',
            'status'     => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(Users::class, ['tenant_id' => 'id']);
    }

    public static function optsStatus()
    {
        return [
            self::STATUS_ACTIVE   => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }
}
