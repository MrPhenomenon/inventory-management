<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property string|null $category
 * @property int $base_unit_id
 * @property string|null $created_at
 *
 * @property Units $baseUnit
 * @property InventoryTransactions[] $inventoryTransactions
 * @property ProductUnits[] $productUnits
 * @property PurchaseItems[] $purchaseItems
 * @property SaleItems[] $saleItems
 */
class Products extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category'], 'default', 'value' => null],
            [['name', 'base_unit_id'], 'required'],
            [['base_unit_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'category'], 'string', 'max' => 100],
            [['base_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Units::class, 'targetAttribute' => ['base_unit_id' => 'id']],
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
            'category' => 'Category',
            'base_unit_id' => 'Base Unit ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[BaseUnit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseUnit()
    {
        return $this->hasOne(Units::class, ['id' => 'base_unit_id']);
    }

    /**
     * Gets query for [[InventoryTransactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryTransactions()
    {
        return $this->hasMany(InventoryTransactions::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductUnits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductUnits()
    {
        return $this->hasMany(ProductUnits::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[PurchaseItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseItems()
    {
        return $this->hasMany(PurchaseItems::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[SaleItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSaleItems()
    {
        return $this->hasMany(SaleItems::class, ['product_id' => 'id']);
    }

}
