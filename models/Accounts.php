<?php

namespace app\models;

use app\components\AppModel;
use Yii;

/**
 * This is the model class for table "accounts".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property float|null $balance
 *
 * @property AccountTransactions[] $accountTransactions
 */
class Accounts extends AppModel
{

    /**
     * ENUM field values
     */
    const TYPE_CASH = 'cash';
    const TYPE_BANK = 'bank';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accounts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance'], 'default', 'value' => 0.00],
            [['name', 'type'], 'required'],
            [['type'], 'string'],
            [['balance'], 'number'],
            [['name'], 'string', 'max' => 100],
            ['type', 'in', 'range' => array_keys(self::optsType())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'balance' => 'Balance',
        ];
    }

    /**
     * Gets query for [[AccountTransactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTransactions()
    {
        return $this->hasMany(AccountTransactions::class, ['account_id' => 'id']);
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_CASH => 'cash',
            self::TYPE_BANK => 'bank',
        ];
    }

    /**
     * @return string
     */
    public function displayType()
    {
        return self::optsType()[$this->type];
    }

    /**
     * @return bool
     */
    public function isTypeCash()
    {
        return $this->type === self::TYPE_CASH;
    }

    public function setTypeToCash()
    {
        $this->type = self::TYPE_CASH;
    }

    /**
     * @return bool
     */
    public function isTypeBank()
    {
        return $this->type === self::TYPE_BANK;
    }

    public function setTypeToBank()
    {
        $this->type = self::TYPE_BANK;
    }
}
