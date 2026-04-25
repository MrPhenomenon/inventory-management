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
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string|null $created_at
 *
 * @property Parties $party
 */
class Payments extends AppModel
{

    /**
     * ENUM field values
     */
    const TYPE_INCOMING = 'incoming';
    const TYPE_OUTGOING = 'outgoing';
    const METHOD_CASH = 'cash';
    const METHOD_BANK = 'bank';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reference_type', 'reference_id'], 'default', 'value' => null],
            [['party_id', 'type', 'amount', 'method'], 'required'],
            [['party_id', 'reference_id'], 'integer'],
            [['type', 'method'], 'string'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 50],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            ['method', 'in', 'range' => array_keys(self::optsMethod())],
            [['party_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parties::class, 'targetAttribute' => ['party_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'party_id' => 'Party ID',
            'type' => 'Type',
            'amount' => 'Amount',
            'method' => 'Method',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Party]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParty()
    {
        return $this->hasOne(Parties::class, ['id' => 'party_id']);
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
     * column method ENUM value labels
     * @return string[]
     */
    public static function optsMethod()
    {
        return [
            self::METHOD_CASH => 'cash',
            self::METHOD_BANK => 'bank',
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

    /**
     * @return string
     */
    public function displayMethod()
    {
        return self::optsMethod()[$this->method];
    }

    /**
     * @return bool
     */
    public function isMethodCash()
    {
        return $this->method === self::METHOD_CASH;
    }

    public function setMethodToCash()
    {
        $this->method = self::METHOD_CASH;
    }

    /**
     * @return bool
     */
    public function isMethodBank()
    {
        return $this->method === self::METHOD_BANK;
    }

    public function setMethodToBank()
    {
        $this->method = self::METHOD_BANK;
    }
}
