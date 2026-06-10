<?php

namespace app\models;

use app\components\AppModel;
use app\models\Purchases;
use app\models\Sales;
use Yii;

/**
 * Single-row config table for the Chemical Account opening balance.
 *
 * @property int $id
 * @property float $opening_balance
 */
class ChemicalAccount extends AppModel
{
    public static function tableName()
    {
        return 'chemical_account';
    }

    public function rules()
    {
        return [
            [['opening_balance'], 'default', 'value' => 0.00],
            [['opening_balance'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'opening_balance' => 'Opening Balance',
        ];
    }

    /**
     * Always returns the single config row, creating it if it doesn't exist.
     */
    public static function getConfig(): self
    {
        $row = static::findOne(1);
        if (!$row) {
            $row = new self();
            $row->id = 1;
            $row->opening_balance = 0;
            $row->save(false);
        }
        return $row;
    }

    /**
     * Dynamic Chemical Account balance:
     *   opening_balance
     *   + total purchase amounts  (buying chemicals increases value)
     *   - total sale amounts      (selling chemicals decreases value)
     *   + total nil additions     (expense accounts settled add back to Chemical)
     *
     * Payments do NOT affect Chemical Account.
     */
    public static function calculateBalance(): float
    {
        $config        = static::getConfig();
        $totalPurchases = Purchases::find()->sum('total_amount') ?? 0;
        $totalSales     = Sales::find()->sum('total_amount') ?? 0;
        $nilAdditions   = NilOperations::find()->sum('expense_total') ?? 0;

        return (float)$config->opening_balance
            + (float)$totalPurchases
            - (float)$totalSales
            + (float)$nilAdditions;
    }
}
