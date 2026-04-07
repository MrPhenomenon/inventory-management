<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sales".
 *
 * @property int $id
 * @property int $customer_id
 * @property float $total_amount
 * @property float|null $paid_amount
 * @property string|null $status
 * @property string|null $sale_date
 * @property string|null $created_at
 *
 * @property Parties $customer
 * @property SaleItems[] $saleItems
 */
class Sales extends \yii\db\ActiveRecord
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
        return 'sales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sale_date'], 'default', 'value' => null],
            [['paid_amount'], 'default', 'value' => 0.00],
            [['status'], 'default', 'value' => 'pending'],
            [['customer_id', 'total_amount'], 'required'],
            [['customer_id'], 'integer'],
            [['total_amount', 'paid_amount'], 'number'],
            [['status'], 'string'],
            [['sale_date', 'created_at'], 'safe'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parties::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'status' => 'Status',
            'sale_date' => 'Sale Date',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Parties::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[SaleItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSaleItems()
    {
        return $this->hasMany(SaleItems::class, ['sale_id' => 'id']);
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
