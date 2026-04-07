<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "units".
 *
 * @property int $id
 * @property string $name
 * @property string $symbol
 *
 * @property Products[] $products
 */
class Units extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'units';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'symbol'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['symbol'], 'string', 'max' => 10],
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
            'symbol' => 'Symbol',
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Products::class, ['base_unit_id' => 'id']);
    }

}
