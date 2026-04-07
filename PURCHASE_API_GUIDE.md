# Purchase Module - API & Code Examples

## Quick Reference

### Create Purchase with Multiple Items

```php
use app\models\Purchases;
use app\models\PurchaseItems;
use app\models\InventoryTransactions;
use Yii;

// In controller action
$transaction = Yii::$app->db->beginTransaction();

try {
    // 1. Create purchase
    $purchase = new Purchases();
    $purchase->supplier_id = 1;
    $purchase->purchase_date = date('Y-m-d H:i:s');
    $purchase->paid_amount = 0;
    
    if (!$purchase->save()) {
        throw new \Exception('Failed to save purchase');
    }
    
    $total_amount = 0;
    
    // 2. Add items and create inventory transactions
    foreach ($items as $item_data) {
        // Get product unit conversion
        $productUnit = ProductUnits::findOne($item_data['product_unit_id']);
        
        // Create purchase item
        $purchase_item = new PurchaseItems();
        $purchase_item->purchase_id = $purchase->id;
        $purchase_item->product_id = $item_data['product_id'];
        $purchase_item->product_unit_id = $item_data['product_unit_id'];
        $purchase_item->quantity = $item_data['quantity'];
        $purchase_item->price = $item_data['price'];
        $purchase_item->total = $item_data['quantity'] * $item_data['price'];
        
        $purchase_item->save();
        
        // Create inventory transaction
        $base_quantity = $item_data['quantity'] * $productUnit->conversion_to_base;
        
        $inventory = new InventoryTransactions();
        $inventory->product_id = $item_data['product_id'];
        $inventory->type = 'in';
        $inventory->quantity = $item_data['quantity'];
        $inventory->product_unit_id = $item_data['product_unit_id'];
        $inventory->base_quantity = $base_quantity;
        $inventory->reference_type = 'purchase';
        $inventory->reference_id = $purchase->id;
        
        $inventory->save();
        
        $total_amount += $purchase_item->total;
    }
    
    // 3. Update purchase totals and status
    $purchase->total_amount = $total_amount;
    
    if ($purchase->paid_amount >= $total_amount) {
        $purchase->status = Purchases::STATUS_PAID;
    } elseif ($purchase->paid_amount > 0) {
        $purchase->status = Purchases::STATUS_PARTIAL;
    } else {
        $purchase->status = Purchases::STATUS_PENDING;
    }
    
    $purchase->save();
    
    $transaction->commit();
    
} catch (\Exception $e) {
    $transaction->rollBack();
    throw $e;
}
```

---

## Model Usage Examples

### Retrieve Purchase with Items

```php
$purchase = Purchases::findOne(1);

// Get supplier
echo $purchase->supplier->name;

// Get all items
foreach ($purchase->purchaseItems as $item) {
    echo $item->product->name;
    echo $item->quantity . ' ' . $item->productUnit->unit_name;
    echo $item->total;
}

// Check status
if ($purchase->isStatusPaid()) {
    echo "Fully Paid";
} elseif ($purchase->isStatusPartial()) {
    echo "Partially Paid";
} else {
    echo "Pending Payment";
}
```

### Get Inventory Transactions for Purchase

```php
use app\models\InventoryTransactions;

$transactions = InventoryTransactions::find()
    ->where(['reference_type' => 'purchase', 'reference_id' => 1])
    ->all();

foreach ($transactions as $tx) {
    echo "Product: " . $tx->product->name;
    echo "Quantity: " . $tx->quantity . " " . $tx->productUnit->unit_name;
    echo "Base Quantity: " . $tx->base_quantity . " " . $tx->product->baseUnit->symbol;
}
```

### Query by Supplier

```php
$purchases = Purchases::find()
    ->where(['supplier_id' => 1])
    ->orderBy(['purchase_date' => SORT_DESC])
    ->all();
```

### Query by Status

```php
// Get all pending purchases
$pending = Purchases::find()
    ->where(['status' => Purchases::STATUS_PENDING])
    ->all();

// Get partial payments
$partial = Purchases::find()
    ->where(['status' => Purchases::STATUS_PARTIAL])
    ->all();
```

### Calculate Outstanding Amount

```php
$purchase = Purchases::findOne(1);
$outstanding = $purchase->total_amount - $purchase->paid_amount;
echo "Still owe: " . $outstanding;
```

---

## AJAX Endpoint Usage

### In JavaScript

```javascript
// Load units for a product
const productId = 1;

fetch(`/productunit/list?product_id=${productId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Populate dropdown
            let options = '<option value="">Select Unit</option>';
            data.data.forEach(unit => {
                options += `<option value="${unit.id}">${unit.unit_name}</option>`;
            });
            document.getElementById('units').innerHTML = options;
        }
    });
```

### In jQuery

```javascript
$.ajax({
    url: '/productunit/list',
    data: { product_id: 1 },
    dataType: 'json',
    success: function(data) {
        if (data.success) {
            let options = '<option>Select Unit</option>';
            $.each(data.data, function(i, unit) {
                options += '<option value="' + unit.id + '">' + unit.unit_name + '</option>';
            });
            $('#units').html(options);
        }
    }
});
```

---

## Validation Examples

### Valid Purchase Item

```php
$item = new PurchaseItems();
$item->purchase_id = 1;
$item->product_id = 1;
$item->product_unit_id = 1;
$item->quantity = 10;      // OK: > 0
$item->price = 100;        // OK: >= 0
$item->total = 1000;

if ($item->validate()) {
    echo "Item is valid!";
}
```

### Invalid Purchase Item (Qty <= 0)

```php
$item = new PurchaseItems();
$item->quantity = 0;       // ERROR!
$item->price = 100;

if (!$item->validate()) {
    print_r($item->errors);
    // Output: [quantity] => [Quantity must be greater than 0]
}
```

### Invalid Purchase Item (Price < 0)

```php
$item = new PurchaseItems();
$item->quantity = 10;
$item->price = -50;        // ERROR!

if (!$item->validate()) {
    print_r($item->errors);
    // Output: [price] => [Price must be greater than or equal to 0]
}
```

---

## Status Management

### Check Status

```php
$purchase = Purchases::findOne(1);

// Using helper methods
if ($purchase->isStatusPending()) {
    // Show payment form
}

if ($purchase->isStatusPartial()) {
    // Show "Pay remaining" button
}

if ($purchase->isStatusPaid()) {
    // Show "Fully paid" badge
}
```

### Update Status Manually

```php
$purchase = Purchases::findOne(1);

// Check if fully paid
if ($purchase->paid_amount >= $purchase->total_amount) {
    $purchase->setStatusToPaid();
    $purchase->save();
}
```

### Get Status Label

```php
$purchase = Purchases::findOne(1);
echo $purchase->displayStatus(); // Returns: "pending", "partial", or "paid"
```

---

## Reporting Examples

### Total Purchases by Supplier

```php
use yii\db\Query;

$result = (new Query())
    ->select(['supplier_id', 'COUNT(*) as count', 'SUM(total_amount) as total'])
    ->from('purchases')
    ->groupBy('supplier_id')
    ->all();

foreach ($result as $row) {
    $supplier = Parties::findOne($row['supplier_id']);
    echo $supplier->name . ": " . $row['count'] . " purchases, Total: " . $row['total'];
}
```

### Purchases Last 30 Days

```php
$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

$purchases = Purchases::find()
    ->where(['>=', 'purchase_date', $thirtyDaysAgo])
    ->orderBy(['purchase_date' => SORT_DESC])
    ->all();
```

### Outstanding Receivables

```php
$outstanding = (new Query())
    ->select(['id', 'supplier_id', 'total_amount', 'paid_amount', 
              'total_amount - paid_amount as balance'])
    ->from('purchases')
    ->where(['<', 'paid_amount', 'total_amount']) // Not fully paid
    ->all();

foreach ($outstanding as $row) {
    echo "Purchase #" . $row['id'] . " owes: " . $row['balance'];
}
```

---

## Transaction Rollback Scenario

```php
$transaction = Yii::$app->db->beginTransaction();

try {
    $purchase = new Purchases();
    $purchase->supplier_id = 1;
    $purchase->save();
    
    // Simulate error
    throw new \Exception('Something went wrong');
    
    $transaction->commit();
} catch (\Exception $e) {
    $transaction->rollBack();
    Yii::error("Purchase creation failed: " . $e->getMessage());
    // Purchase and all items are NOT saved
}
```

---

## Performance Tips

### For Large Purchases with Many Items

```php
// Use transaction with validation before save
$transaction = Yii::$app->db->beginTransaction();

try {
    // Validate all items first
    foreach ($items as $item_data) {
        $item = new PurchaseItems();
        $item->load($item_data);
        if (!$item->validate()) {
            throw new \Exception("Invalid item data");
        }
    }
    
    // Then save (if all valid)
    foreach ($items as $item_data) {
        $item->save();
    }
    
    $transaction->commit();
} catch (\Exception $e) {
    $transaction->rollBack();
}
```

### Eager Load Relations

```php
$purchase = Purchases::find()
    ->with('supplier', 'purchaseItems.product', 'purchaseItems.productUnit')
    ->where(['id' => 1])
    ->one();

// Now accessing relations won't trigger additional queries
echo $purchase->supplier->name;
foreach ($purchase->purchaseItems as $item) {
    echo $item->product->name;
}
```

---

## Integration with Payment Module

```php
use app\models\Payments;

$purchase = Purchases::findOne(1);

// Create payment record
$payment = new Payments();
$payment->purchase_id = $purchase->id;
$payment->supplier_id = $purchase->supplier_id;
$payment->amount = 100;
$payment->save();

// Update purchase paid amount
$purchase->paid_amount += $payment->amount;

// Auto-update status
if ($purchase->paid_amount >= $purchase->total_amount) {
    $purchase->status = Purchases::STATUS_PAID;
} elseif ($purchase->paid_amount > 0) {
    $purchase->status = Purchases::STATUS_PARTIAL;
}

$purchase->save();
```

---

**Version:** 1.0 | **Last Updated:** March 30, 2026
