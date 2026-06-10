<?php

namespace app\models;

use app\components\AppModel;
use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $party_id
 * @property string $type
 * @property float $amount
 * @property string $method
 * @property int|null $account_id
 * @property string|null $notes
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string|null $created_at
 *
 * @property Parties $party
 * @property Accounts $account
 */
class Payments extends AppModel
{
    const TYPE_INCOMING = 'incoming';
    const TYPE_OUTGOING = 'outgoing';

    public static function tableName()
    {
        return 'payments';
    }

    public function rules()
    {
        return [
            [['reference_type', 'reference_id', 'account_id', 'notes'], 'default', 'value' => null],
            [['party_id', 'type', 'amount'], 'required'],
            [['party_id', 'reference_id', 'account_id'], 'integer'],
            [['type'], 'string'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 50],
            [['notes'], 'string', 'max' => 255],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            [['party_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parties::class, 'targetAttribute' => ['party_id' => 'id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accounts::class, 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'party_id'       => 'Party',
            'type'           => 'Type',
            'amount'         => 'Amount',
            'account_id'     => 'Account',
            'notes'          => 'Notes',
            'reference_type' => 'Reference Type',
            'reference_id'   => 'Reference ID',
            'created_at'     => 'Date',
        ];
    }

    public function getParty()
    {
        return $this->hasOne(Parties::class, ['id' => 'party_id']);
    }

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
            self::TYPE_INCOMING => 'incoming',
            self::TYPE_OUTGOING => 'outgoing',
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
    public function isTypeIncoming()
    {
        return $this->type === self::TYPE_INCOMING;
    }

    public function setTypeToIncoming()
    {
        $this->type = self::TYPE_INCOMING;
    }

    /**
     * @return bool
     */
    public function isTypeOutgoing()
    {
        return $this->type === self::TYPE_OUTGOING;
    }

    public function setTypeToOutgoing()
    {
        $this->type = self::TYPE_OUTGOING;
    }
}
