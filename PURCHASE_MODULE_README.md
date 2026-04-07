# Purchase Module - Implementation Guide

## ✅ Completed Deliverables

### 1. PurchaseController
**File:** [controllers/PurchaseController.php](controllers/PurchaseController.php)

#### Actions:
- **actionIndex()** - Lists all purchases with pagination
  - Displays: Supplier, Purchase Date, Total Amount, Paid Amount, Status
  - Sorted by newest first
  - Status badges color-coded (pending=yellow, partial=blue, paid=green)

- **actionCreate()** - Creates purchase with items and inventory transactions
  - Uses DB transaction for atomic operations
  - Accepts multiple items
  - Calculates base_quantity from product_units conversion
  - Creates inventory transactions (type='in')
  - Updates status based on payment
  - Rollback on any error

- **actionView($id)** - Displays purchase details
  - Shows purchase info, payment status, and all items
  - Displays outstanding amount
  - Shows item breakdown with products and units

### 2. Views
**Location:** `views/purchase/`

#### **index.php** - Purchase Listing
- GridView table showing all purchases
- Columns: ID, Supplier, Purchase Date, Total, Paid, Status, Actions
- Status badges (color-coded)
- Create button
- Delete button with confirmation
- Pjax support for smooth interactions

#### **create.php** - Purchase Creation with Items
**Features:**
- Supplier dropdown (filtered for suppliers and both types)
- Purchase date input (datetime-local)
- Paid amount input
- **Dynamic Items Table:**
  - Product dropdown (populated from all products)
  - Unit dropdown (dynamically loaded via AJAX)
  - Quantity input (decimal)
  - Price input (decimal)
  - Row total (auto-calculated)
  - Remove button for each row
- Add Item button (adds new row)
- Auto-calculated Grand Total
- JavaScript handles:
  - Dynamic row addition/removal
  - AJAX loading of units based on product
  - Automatic calculation of row totals
  - Grand total calculation

#### **view.php** - Purchase Details
- Purchase information card (supplier, dates)
- Payment status card (total, paid, outstanding, status badge)
- Purchase items table (product, unit, quantity, price, total)
- Back and Delete buttons

### 3. Business Logic Implementation

#### Transaction Flow (Purchase Creation)
```
1. Validate input
   ├─ Check supplier_id exists
   └─ Ensure at least 1 item
   
2. Start DB Transaction
   
3. Save Purchase record
   
4. For each item:
   ├─ Get ProductUnit (for conversion_to_base)
   ├─ Calculate base_quantity = quantity × conversion_to_base
   ├─ Save PurchaseItem
   ├─ Create InventoryTransaction (type='in')
   │  ├─ product_id
   │  ├─ quantity (in unit)
   │  ├─ product_unit_id
   │  ├─ base_quantity (converted to base unit)
   │  ├─ reference_type = 'purchase'
   │  └─ reference_id = purchase_id
   └─ Add to total_amount
   
5. Calculate Status:
   ├─ If paid_amount >= total_amount → 'paid'
   ├─ Else if paid_amount > 0 → 'partial'
   └─ Else → 'pending'
   
6. Update Purchase with totals and status
   
7. Commit Transaction
   └─ On error: Rollback all
```

### 4. Database Models Used

#### Purchases
- id, supplier_id, total_amount, paid_amount, status, purchase_date, created_at
- Constants: STATUS_PENDING, STATUS_PARTIAL, STATUS_PAID
- Methods: displayStatus(), isStatusPending(), etc.

#### PurchaseItems (Enhanced)
- id, purchase_id, product_id, product_unit_id, quantity, price, total
- **New Validation:** quantity > 0, price >= 0

#### InventoryTransactions
- id, product_id, type='in', quantity, product_unit_id, base_quantity, reference_type='purchase', reference_id

#### Supporting Models:
- **Parties** (Suppliers)
- **Products**
- **ProductUnits** (for conversion factors)

---

## 🛣️ URL Routes

Convention-based routing (no custom routes needed):

```
/purchase/index                    - List all purchases
/purchase/create                   - Create new purchase
/purchase/view?id=X                - View purchase details
/purchase/delete?id=X              - Delete purchase (POST)
```

---

## 🌐 AJAX Integration

### Product Unit Listing Endpoint
**Endpoint:** `GET /productunit/list`

**Usage:** Automatically called when product is selected in purchase create form

**Request:**
```javascript
GET /productunit/list?product_id=1
```

**Response:**
```json
{
  "success": true,
  "data": [
    {"id": 1, "product_id": 1, "unit_name": "Drum", "conversion_to_base": 250},
    {"id": 2, "product_id": 1, "unit_name": "Bottle", "conversion_to_base": 1}
  ]
}
```

---

## 🧪 Test Scenario

1. **Create Units** (if not exists)
   - Via Product module or direct DB insert
   - Example: Phenol with base unit kg
   - Add unit: Drum = 250kg, Bottle = 1kg

2. **Create Suppliers** (via Parties)
   - Name: "Chemical Co"
   - Type: "supplier"

3. **Create Purchase**
   - Go to `/purchase/create`
   - Select supplier: "Chemical Co"
   - Set purchase date
   - Add Item 1:
     - Product: Phenol
     - Unit: Drum (automatically loaded)
     - Quantity: 10
     - Price: 100
     - Row Total: 1000 (auto-calculated)
   - Add Item 2:
     - Product: Phenol
     - Unit: Bottle
     - Quantity: 50
     - Price: 50
     - Row Total: 2500 (auto-calculated)
   - Grand Total: 3500 (auto-calculated)
   - Click "Create Purchase"

4. **Verify Results**
   - View purchase details at `/purchase/view?id=1`
   - Check inventory_transactions table:
     - 2 records created (for 2 items)
     - Type = 'in'
     - base_quantity = quantity × conversion_to_base

---

## 📊 Example: Inventory Transaction Calculation

### Purchase Item
```
Product: Phenol (base unit = kg)
Unit: Drum (1 Drum = 250 kg)
Quantity: 10 Drums
```

### Created Inventory Transaction
```
product_id: 1 (Phenol)
type: 'in'
quantity: 10 (in Drums)
product_unit_id: 1 (Drum)
base_quantity: 10 × 250 = 2500 (in kg)
reference_type: 'purchase'
reference_id: 1 (Purchase ID)
```

---

## 🔐 Security Features

✅ **CSRF Protection** - Enabled by default
✅ **Verb Filtering** - DELETE requires POST
✅ **Input Validation** - All models validated
✅ **SQL Injection Prevention** - Using ActiveRecord
✅ **Transaction Safety** - Atomic operations with rollback

---

## 🧠 Code Quality

✅ **Thin Controllers** - Business logic in models
✅ **ACID Compliance** - Database transactions
✅ **Proper Error Handling** - Try-catch with rollback
✅ **Yii2 Conventions** - Standard patterns followed
✅ **Reusable Components** - AJAX endpoint reused
✅ **Dynamic UI** - JavaScript for item management

---

## 🐛 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Product dropdown shows no products | Check products table has data |
| Unit dropdown not loading | Verify /productunit/list endpoint works, check product_id passed correctly |
| Status not updating | Confirm paid_amount is set, check status enum validation |
| Inventory transaction not created | Check transaction didn't rollback, verify product_unit_id exists |
| Transaction error | Check all foreign keys exist, verify supplier_id is valid |

---

## 🚀 Performance Notes

- Purchase listing uses pagination (20 per page)
- AJAX endpoint optimized for single product
- Inventory transactions created in loop (consider batch for large volumes)
- Status badges rendered server-side for caching

---

## 📝 Validation Rules

### Purchase Creation
```
supplier_id: required, must exist in parties
paid_amount: default 0, must be number >= 0
purchase_date: defaults to current datetime
```

### Purchase Items
```
For each item:
  product_id: required, must exist
  product_unit_id: required, must exist
  quantity: required, number > 0
  price: required, number >= 0
  total: auto-calculated (quantity × price)
```

### Status Calculation
```
If paid_amount >= total_amount → 'paid'
Else if paid_amount > 0 → 'partial'
Else → 'pending'
```

---

## 🔗 Integration Points

This module integrates with:
1. **Product Module** - Products and units for selection
2. **Inventory Module** - Creates IN transactions
3. **Parties Module** - Supplier selection
4. **Payment Module** - Can track payments against purchases

---

## 📦 Files Created/Modified

```
controllers/
  └─ PurchaseController.php                           ✅ Created

views/
  └─ purchase/
     ├─ index.php                                    ✅ Created
     ├─ create.php                                   ✅ Created
     └─ view.php                                     ✅ Created

models/
  └─ PurchaseItems.php                               ✅ Enhanced (validation)
```

---

**Version:** 1.0 | **Status:** ✅ FULLY IMPLEMENTED | **Date:** March 30, 2026
