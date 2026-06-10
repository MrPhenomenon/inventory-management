<?php

namespace app\models;

use app\components\AppModel;

/**
 * @property int $id
 * @property int $category_id
 * @property float $chemical_balance_before
 * @property float $expense_total
 * @property float $chemical_balance_after
 * @property int $performed_by
 * @property string|null $notes
 * @property string|null $created_at
 *
 * @property ExpenseCategories $category
 * @property Users $performedBy
 */
class NilOperations extends AppModel
{
    public static function tableName()
    {
        return 'nil_operations';
    }

    public function rules()
    {
        return [
            [['category_id', 'chemical_balance_before', 'expense_total', 'chemical_balance_after', 'performed_by'], 'required'],
            [['chemical_balance_before', 'expense_total', 'chemical_balance_after'], 'number'],
            [['category_id', 'performed_by'], 'integer'],
            [['notes'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ExpenseCategories::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                      => 'ID',
            'category_id'             => 'Category',
            'chemical_balance_before' => 'Chemical Balance Before',
            'expense_total'           => 'Amount Deducted',
            'chemical_balance_after'  => 'Chemical Balance After',
            'performed_by'            => 'Performed By',
            'notes'                   => 'Notes',
            'created_at'              => 'Date',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(ExpenseCategories::class, ['id' => 'category_id']);
    }

    public function getPerformedBy()
    {
        return $this->hasOne(Users::class, ['id' => 'performed_by']);
    }
}
