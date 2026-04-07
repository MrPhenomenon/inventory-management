<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "parties".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $created_at
 *
 * @property Payments[] $payments
 * @property Purchases[] $purchases
 * @property Sales[] $sales
 */
class Parties extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_CUSTOMER = 'customer';
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_BOTH = 'both';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'address'], 'default', 'value' => null],
            [['name', 'type'], 'required'],
            [['type', 'address'], 'string'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 150],
            [['phone'], 'string', 'max' => 20],
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
            'phone' => 'Phone',
            'address' => 'Address',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Payments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::class, ['party_id' => 'id']);
    }

    /**
     * Gets query for [[Purchases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchases()
    {
        return $this->hasMany(Purchases::class, ['supplier_id' => 'id']);
    }

    /**
     * Gets query for [[Sales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sales::class, ['customer_id' => 'id']);
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_CUSTOMER => 'customer',
            self::TYPE_SUPPLIER => 'supplier',
            self::TYPE_BOTH => 'both',
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
    public function isTypeCustomer()
    {
        return $this->type === self::TYPE_CUSTOMER;
    }

    public function setTypeToCustomer()
    {
        $this->type = self::TYPE_CUSTOMER;
    }

    /**
     * @return bool
     */
    public function isTypeSupplier()
    {
        return $this->type === self::TYPE_SUPPLIER;
    }

    public function setTypeToSupplier()
    {
        $this->type = self::TYPE_SUPPLIER;
    }

    /**
     * @return bool
     */
    public function isTypeBoth()
    {
        return $this->type === self::TYPE_BOTH;
    }

    public function setTypeToBoth()
    {
        $this->type = self::TYPE_BOTH;
    }
}
