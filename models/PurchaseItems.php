<?php

namespace app\models;

use app\components\AppModel;
use Yii;

/**
 * This is the model class for table "purchase_items".
 *
 * @property int $id
 * @property int $purchase_id
 * @property int $product_id
 * @property int $product_unit_id
 * @property float $quantity
 * @property float $price
 * @property float $total
 *
 * @property Products $product
 * @property ProductUnits $productUnit
 * @property Purchases $purchase
 */
class PurchaseItems extends AppModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id', 'product_id', 'product_unit_id', 'quantity', 'price', 'total'], 'required'],
            [['purchase_id', 'product_id', 'product_unit_id'], 'integer'],
            [['quantity', 'price', 'total'], 'number'],
            [['quantity'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'Quantity must be greater than 0'],
            [['price'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => 'Price must be greater than or equal to 0'],
            [['purchase_id'], 'exist', 'skipOnError' => true, 'targetClass' => Purchases::class, 'targetAttribute' => ['purchase_id' => 'id']],
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
            'purchase_id' => 'Purchase ID',
            'product_id' => 'Product ID',
            'product_unit_id' => 'Product Unit ID',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'total' => 'Total',
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
     * Gets query for [[Purchase]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchase()
    {
        return $this->hasOne(Purchases::class, ['id' => 'purchase_id']);
    }

}
