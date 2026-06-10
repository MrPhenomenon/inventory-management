<?php

namespace app\models;

use app\components\AppModel;

/**
 * @property int $id
 * @property int $category_id
 * @property float $amount
 * @property string $description
 * @property int $paid_from_account_id
 * @property string|null $created_at
 *
 * @property ExpenseCategories $category
 * @property Accounts $paidFromAccount
 */
class Expenses extends AppModel
{
    public static function tableName()
    {
        return 'expenses';
    }

    public function rules()
    {
        return [
            [['category_id', 'amount', 'description', 'paid_from_account_id'], 'required'],
            [['category_id', 'paid_from_account_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ExpenseCategories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['paid_from_account_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Accounts::class, 'targetAttribute' => ['paid_from_account_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'category_id'          => 'Category',
            'amount'               => 'Amount',
            'description'          => 'Description',
            'paid_from_account_id' => 'Paid From',
            'created_at'           => 'Date',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(ExpenseCategories::class, ['id' => 'category_id']);
    }

    public function getPaidFromAccount()
    {
        return $this->hasOne(Accounts::class, ['id' => 'paid_from_account_id']);
    }

    /**
     * Sum of expenses for a category since the last nil operation for that category.
     * Returns the "pending" amount that hasn't been settled against Chemical Account yet.
     */
    public static function currentTotal(int $categoryId): float
    {
        $query = static::find()->where(['category_id' => $categoryId]);

        $lastNil = NilOperations::find()
            ->where(['category_id' => $categoryId])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($lastNil) {
            $query->andWhere(['>', 'created_at', $lastNil->created_at]);
        }

        return (float)($query->sum('amount') ?? 0);
    }
}
