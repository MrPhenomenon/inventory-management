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
 * @property float $opening_balance
 *
 * @property AccountTransactions[] $accountTransactions
 */
class Accounts extends AppModel
{
    const TYPE_CASH = 'cash';
    const TYPE_BANK = 'bank';

    public static function tableName()
    {
        return 'accounts';
    }

    public function rules()
    {
        return [
            [['opening_balance'], 'default', 'value' => 0.00],
            [['name', 'type'], 'required'],
            [['type'], 'string'],
            [['opening_balance'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 100],
            ['type', 'in', 'range' => array_keys(self::optsType())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'name'            => 'Name',
            'type'            => 'Type',
            'opening_balance' => 'Opening Balance',
        ];
    }

    public function getAccountTransactions()
    {
        return $this->hasMany(AccountTransactions::class, ['account_id' => 'id']);
    }

    /**
     * Dynamic balance: opening_balance + all credits - all debits
     */
    public function getBalance()
    {
        $credits = AccountTransactions::find()
            ->where(['account_id' => $this->id, 'type' => AccountTransactions::TYPE_CREDIT])
            ->sum('amount') ?? 0;

        $debits = AccountTransactions::find()
            ->where(['account_id' => $this->id, 'type' => AccountTransactions::TYPE_DEBIT])
            ->sum('amount') ?? 0;

        return (float)$this->opening_balance + (float)$credits - (float)$debits;
    }

    public static function optsType()
    {
        return [
            self::TYPE_CASH => 'Cash',
            self::TYPE_BANK => 'Bank',
        ];
    }

    public function displayType()
    {
        return self::optsType()[$this->type] ?? $this->type;
    }

    public function isTypeCash()
    {
        return $this->type === self::TYPE_CASH;
    }

    public function isTypeBank()
    {
        return $this->type === self::TYPE_BANK;
    }
}
