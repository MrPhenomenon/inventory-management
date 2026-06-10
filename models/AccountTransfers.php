<?php

namespace app\models;

use app\components\AppModel;

/**
 * This is the model class for table "account_transfers".
 * Transfers between sub-accounts (e.g. Meezan → Cash).
 * Does NOT affect Chemical Account.
 *
 * @property int $id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property float $amount
 * @property string|null $description
 * @property string|null $created_at
 *
 * @property Accounts $fromAccount
 * @property Accounts $toAccount
 */
class AccountTransfers extends AppModel
{
    public static function tableName()
    {
        return 'account_transfers';
    }

    public function rules()
    {
        return [
            [['from_account_id', 'to_account_id', 'amount'], 'required'],
            [['from_account_id', 'to_account_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
            ['from_account_id', 'compare', 'compareAttribute' => 'to_account_id', 'operator' => '!=', 'message' => 'Source and destination accounts must be different.'],
            [['from_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accounts::class, 'targetAttribute' => ['from_account_id' => 'id']],
            [['to_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accounts::class, 'targetAttribute' => ['to_account_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'from_account_id' => 'From Account',
            'to_account_id'   => 'To Account',
            'amount'          => 'Amount',
            'description'     => 'Description',
            'created_at'      => 'Date',
        ];
    }

    public function getFromAccount()
    {
        return $this->hasOne(Accounts::class, ['id' => 'from_account_id']);
    }

    public function getToAccount()
    {
        return $this->hasOne(Accounts::class, ['id' => 'to_account_id']);
    }
}
