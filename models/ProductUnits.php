<?php

namespace app\models;

use app\components\AppModel;
use Yii;

/**
 * This is the model class for table "product_units".
 *
 * @property int $id
 * @property int $product_id
 * @property string $unit_name
 * @property float $conversion_to_base
 *
 * @property InventoryTransactions[] $inventoryTransactions
 * @property Products $product
 * @property PurchaseItems[] $purchaseItems
 * @property SaleItems[] $saleItems
 */
class ProductUnits extends AppModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_units';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'unit_name', 'conversion_to_base'], 'required'],
            [['product_id'], 'integer'],
            [['conversion_to_base'], 'number'],
            [['conversion_to_base'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'Conversion to base must be greater than 0'],
            [['unit_name'], 'string', 'max' => 50],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],
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
            'unit_name' => 'Unit Name',
            'conversion_to_base' => 'Conversion To Base',
        ];
    }

    /**
     * Gets query for [[InventoryTransactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryTransactions()
    {
        return $this->hasMany(InventoryTransactions::class, ['product_unit_id' => 'id']);
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
     * Gets query for [[PurchaseItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseItems()
    {
        return $this->hasMany(PurchaseItems::class, ['product_unit_id' => 'id']);
    }

    /**
     * Gets query for [[SaleItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSaleItems()
    {
        return $this->hasMany(SaleItems::class, ['product_unit_id' => 'id']);
    }

}
