<?php

namespace app\models;

use app\components\AppModel;

/**
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 *
 * @property Expenses[] $expenses
 */
class ExpenseCategories extends AppModel
{
    public static function tableName()
    {
        return 'expense_categories';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Category Name',
            'created_at' => 'Created',
        ];
    }

    public function getExpenses()
    {
        return $this->hasMany(Expenses::class, ['category_id' => 'id']);
    }
}
