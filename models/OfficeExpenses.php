<?php

namespace app\models;

use app\components\AppModel;

/**
 * This is the model class for table "office_expenses".
 *
 * @property int $id
 * @property float $amount
 * @property string $description
 * @property string|null $created_at
 */
class OfficeExpenses extends AppModel
{
    public static function tableName()
    {
        return 'office_expenses';
    }

    public function rules()
    {
        return [
            [['amount', 'description'], 'required'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'amount'      => 'Amount',
            'description' => 'Description',
            'created_at'  => 'Date',
        ];
    }

    /**
     * Running total of all office expenses since last nil operation (or ever).
     * We consider "since last nil" by excluding expenses whose created_at
     * is on or before the last nil operation date.
     */
    public static function currentTotal(): float
    {
        $query = static::find();

        $lastNil = NilOperations::find()->orderBy(['id' => SORT_DESC])->one();
        if ($lastNil) {
            $query->andWhere(['>', 'created_at', $lastNil->created_at]);
        }

        return (float)($query->sum('amount') ?? 0);
    }
}
