<?php

namespace app\models;

use app\components\AppModel;
use Yii;

/**
 * This is the model class for table "account_transactions".
 *
 * @property int $id
 * @property int $account_id
 * @property string $type
 * @property float $amount
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string|null $created_at
 *
 * @property Accounts $account
 */
class AccountTransactions extends AppModel
{

    /**
     * ENUM field values
     */
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reference_type', 'reference_id'], 'default', 'value' => null],
            [['account_id', 'type', 'amount'], 'required'],
            [['account_id', 'reference_id'], 'integer'],
            [['type'], 'string'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 50],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accounts::class, 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'type' => 'Type',
            'amount' => 'Amount',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Accounts::class, ['id' => 'account_id']);
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_CREDIT => 'credit',
            self::TYPE_DEBIT => 'debit',
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
    public function isTypeCredit()
    {
        return $this->type === self::TYPE_CREDIT;
    }

    public function setTypeToCredit()
    {
        $this->type = self::TYPE_CREDIT;
    }

    /**
     * @return bool
     */
    public function isTypeDebit()
    {
        return $this->type === self::TYPE_DEBIT;
    }

    public function setTypeToDebit()
    {
        $this->type = self::TYPE_DEBIT;
    }
}
