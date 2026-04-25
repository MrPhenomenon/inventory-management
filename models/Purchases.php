<?php

namespace app\models;

use app\components\AppModel;
use Yii;

/**
 * This is the model class for table "purchases".
 *
 * @property int $id
 * @property int $supplier_id
 * @property float $total_amount
 * @property float|null $paid_amount
 * @property string|null $status
 * @property string|null $purchase_date
 * @property string|null $created_at
 *
 * @property PurchaseItems[] $purchaseItems
 * @property Parties $supplier
 */
class Purchases extends AppModel
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_date'], 'default', 'value' => null],
            [['paid_amount'], 'default', 'value' => 0.00],
            [['status'], 'default', 'value' => 'pending'],
            [['supplier_id', 'total_amount'], 'required'],
            [['supplier_id'], 'integer'],
            [['total_amount', 'paid_amount'], 'number'],
            [['status'], 'string'],
            [['purchase_date', 'created_at'], 'safe'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parties::class, 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_id' => 'Supplier ID',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'status' => 'Status',
            'purchase_date' => 'Purchase Date',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[PurchaseItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseItems()
    {
        return $this->hasMany(PurchaseItems::class, ['purchase_id' => 'id']);
    }

    /**
     * Gets query for [[Supplier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Parties::class, ['id' => 'supplier_id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_PARTIAL => 'partial',
            self::STATUS_PAID => 'paid',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusPartial()
    {
        return $this->status === self::STATUS_PARTIAL;
    }

    public function setStatusToPartial()
    {
        $this->status = self::STATUS_PARTIAL;
    }

    /**
     * @return bool
     */
    public function isStatusPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function setStatusToPaid()
    {
        $this->status = self::STATUS_PAID;
    }
}
