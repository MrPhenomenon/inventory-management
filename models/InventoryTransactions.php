<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory_transactions".
 *
 * @property int $id
 * @property int $product_id
 * @property string $type
 * @property float $quantity
 * @property int $product_unit_id
 * @property float $base_quantity
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string|null $created_at
 *
 * @property Products $product
 * @property ProductUnits $productUnit
 */
class InventoryTransactions extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    const TYPE_ADJUSTMENT = 'adjustment';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reference_type', 'reference_id'], 'default', 'value' => null],
            [['product_id', 'type', 'quantity', 'product_unit_id', 'base_quantity'], 'required'],
            [['product_id', 'product_unit_id', 'reference_id'], 'integer'],
            [['type'], 'string'],
            [['quantity', 'base_quantity'], 'number'],
            [['created_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 50],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],
            [['product_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductUnits::class, 'targetAttribute' => ['product_unit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'type' => 'Type',
            'quantity' => 'Quantity',
            'product_unit_id' => 'Product Unit ID',
            'base_quantity' => 'Base Quantity',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductUnit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductUnit()
    {
        return $this->hasOne(ProductUnits::class, ['id' => 'product_unit_id']);
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_IN => 'in',
            self::TYPE_OUT => 'out',
            self::TYPE_ADJUSTMENT => 'adjustment',
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
    public function isTypeIn()
    {
        return $this->type === self::TYPE_IN;
    }

    public function setTypeToIn()
    {
        $this->type = self::TYPE_IN;
    }

    /**
     * @return bool
     */
    public function isTypeOut()
    {
        return $this->type === self::TYPE_OUT;
    }

    public function setTypeToOut()
    {
        $this->type = self::TYPE_OUT;
    }

    /**
     * @return bool
     */
    public function isTypeAdjustment()
    {
        return $this->type === self::TYPE_ADJUSTMENT;
    }

    public function setTypeToAdjustment()
    {
        $this->type = self::TYPE_ADJUSTMENT;
    }
}
