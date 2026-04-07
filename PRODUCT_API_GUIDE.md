# Product Module - Developer API Guide

## Quick Start

### Accessing the Product Module

**Admin Panel URLs:**
```
Dashboard: /product/index
Create Product: /product/create
Edit Product: /product/update?id=1
View Units: /productunit/index?product_id=1
Add Unit: /productunit/create?product_id=1
```

---

## Controller Usage Examples

### ProductController

#### Get All Products
```php
use app\models\Products;

$products = Products::find()->all();
foreach ($products as $product) {
    echo $product->name . " (Base Unit: " . $product->baseUnit->name . ")";
}
```

#### Create a Product
```php
$product = new Products();
$product->name = 'Phenol';
$product->category = 'Chemicals';
$product->base_unit_id = 1; // kg
$product->save();
```

#### Access Product Units
```php
$product = Products::findOne(1);
$units = $product->productUnits; // Get all units for this product

foreach ($units as $unit) {
    echo $unit->unit_name . " = " . $unit->conversion_to_base . " " . $product->baseUnit->symbol;
}
// Output: Drum = 250 kg, Bottle = 1 kg
```

---

### ProductunitController

#### Get Units for a Product
```php
use app\models\ProductUnits;

$units = ProductUnits::find()
    ->where(['product_id' => 1])
    ->all();
```

#### Create a Unit
```php
$unit = new ProductUnits();
$unit->product_id = 1;
$unit->unit_name = 'Drum';
$unit->conversion_to_base = 250;
$unit->save();
```

#### Validate Conversion Value
```php
$unit = new ProductUnits();
$unit->product_id = 1;
$unit->unit_name = 'Drum';
$unit->conversion_to_base = -5; // Invalid!

if (!$unit->save()) {
    print_r($unit->errors); 
    // Output: [['conversion_to_base' => ['Conversion to base must be greater than 0']]]
}
```

---

## AJAX API Endpoints

### List Product Units (JSON)

**Endpoint:** `GET /productunit/list`

**Parameters:**
```
product_id (required) - The product ID
```

**Example Request:**
```javascript
fetch('/productunit/list?product_id=1')
    .then(response => response.json())
    .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "unit_name": "Drum",
      "conversion_to_base": 250
    },
    {
      "id": 2,
      "product_id": 1,
      "unit_name": "Bottle",
      "conversion_to_base": 1
    }
  ]
}
```

**jQuery Example:**
```javascript
$.ajax({
    url: '/productunit/list',
    data: { product_id: 1 },
    dataType: 'json',
    success: function(result) {
        if (result.success) {
            // Populate dropdown
            $.each(result.data, function(i, unit) {
                $('#unit-select').append(
                    $('<option>').val(unit.id).text(unit.unit_name)
                );
            });
        }
    }
});
```

---

## Model Relationships

### From Product to Units

```php
// Get base unit
$product = Products::findOne(1);
$baseUnit = $product->baseUnit; // Returns Units model
echo $baseUnit->name; // "kg"

// Get all packaging units
$productUnits = $product->productUnits; // Returns array of ProductUnits

// Get specific information
echo $product->name;           // "Phenol"
echo $product->category;       // "Chemicals"
echo $product->baseUnit->symbol; // "kg"
```

### From Unit to Product

```php
$unit = ProductUnits::findOne(1);
$product = $unit->product; // Returns Products model
echo $product->name; // "Phenol"
```

---

## Form Usage in Views

### Dropdown for Base Units

```php
<?= $form->field($model, 'base_unit_id')->dropDownList(
    yii\helpers\ArrayHelper::map(Units::find()->all(), 'id', 'name'),
    ['prompt' => 'Select a unit']
) ?>
```

### Conversion Input

```php
<?= $form->field($model, 'conversion_to_base')
    ->textInput(['type' => 'number', 'step' => '0.001']) ?>
```

---

## Common Queries

### Find Product by Name
```php
$product = Products::find()->where(['name' => 'Phenol'])->one();
```

### List Products by Category
```php
$products = Products::find()
    ->where(['category' => 'Chemicals'])
    ->all();
```

### Get Units with Conversion > 1
```php
$units = ProductUnits::find()
    ->where(['>', 'conversion_to_base', 1])
    ->all();
```

### Get Product with All Relations
```php
$product = Products::findOne(1);
$product->getProductUnits()->with('product')->all(); // Eager loading
```

---

## Validation Examples

### Valid Product
```php
$product = new Products();
$product->name = 'Phenol';
$product->base_unit_id = 1;

if ($product->validate()) {
    echo "Product is valid!";
}
```

### Invalid Unit (conversion <= 0)
```php
$unit = new ProductUnits();
$unit->product_id = 1;
$unit->unit_name = 'Test';
$unit->conversion_to_base = 0; // Invalid!

if (!$unit->validate()) {
    // Output: Array ( [conversion_to_base] => Array ( [0] => Conversion to base must be greater than 0 ) )
    print_r($unit->errors);
}
```

---

## Error Handling

### 404 Errors
```php
// In ProductController or ProductunitController
// These throw NotFoundHttpException:

// Product not found
$product = Products::findOne(9999); // Returns NULL
// Controller method will throw NotFoundHttpException

// Unit not found
$unit = ProductUnits::findOne(9999); // Returns NULL
// Controller method will throw NotFoundHttpException
```

---

## Performance Tips

### For Large Datasets
```php
// Use pagination
$query = Products::find();
$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => $query,
    'pagination' => ['pageSize' => 50],
]);

// Use eager loading instead of lazy loading
$products = Products::find()
    ->with('baseUnit', 'productUnits')
    ->all();
```

### Cache Product List
```php
$cache = \Yii::$app->cache;
$products = $cache->getOrSet('products_list', function() {
    return Products::find()->all();
}, 3600); // Cache for 1 hour
```

---

## Integration Example

**In Sales Module (for purchase orders):**

```php
use app\models\ProductUnits;

// Calculate order quantity in base units
$unit = ProductUnits::findOne(5); // "Drum" = 250kg
$order_quantity = 2; // 2 drums

$quantity_in_base = $order_quantity * $unit->conversion_to_base; // 500kg
echo "Total: " . $quantity_in_base . " " . $unit->product->baseUnit->symbol;
// Output: Total: 500 kg
```

---

**Version:** 1.0 | **Last Updated:** March 30, 2026
