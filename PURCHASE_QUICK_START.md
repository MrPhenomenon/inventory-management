# Purchase Module - Quick Start Guide

## 🚀 Getting Started

### URLs

```
List purchases:       http://localhost/purchase/index
Create purchase:     http://localhost/purchase/create
View purchase:       http://localhost/purchase/view?id=1
Delete purchase:     http://localhost/purchase/delete?id=1
```

---

## 📋 Setup Checklist

Before creating your first purchase, ensure you have:

- [ ] At least one **Supplier** in Parties (type: supplier or both)
- [ ] At least one **Product** 
- [ ] At least one **Unit** for each product
- [ ] At least one **ProductUnit** (packaging) for each product

---

## 🔄 Purchase Creation Flow

### Step 1: Access Create Form
```
Navigate to: /purchase/create
```

### Step 2: Select Supplier
```
Click dropdown → Select from existing suppliers
If no suppliers, create one in Parties module first
```

### Step 3: Set Purchase Date & Payment
```
Purchase Date: Auto-fills current date, or select custom date
Paid Amount: Leave 0 for pending, or enter initial payment
```

### Step 4: Add Items
```
Button: "Add Item"
For each item:
  - Product: Select product
  - Unit: Automatically loads units for selected product (AJAX)
  - Quantity: Enter quantity
  - Price: Enter unit price
  - Total: Auto-calculated (quantity × price)
```

### Step 5: Review Totals
```
Grand Total: Auto-calculates sum of all items
```

### Step 6: Create
```
Button: "Create Purchase"
Result: Purchase saved, items created, inventory updated
```

---

## 🧮 Auto-Calculations

### Per Item
```
Row Total = Quantity × Price
```

### Grand Total
```
Sum of all Row Totals
```

### Base Quantity (for inventory)
```
Base Quantity = Quantity × Conversion Factor
Example:
  Product: Phenol (base: kg)
  Unit: Drum (1 Drum = 250 kg)
  Quantity: 10 Drums
  Base Quantity: 10 × 250 = 2500 kg
```

---

## 📊 What Happens Behind the Scenes

1. **Purchase Record Created**
   - supplier_id, purchase_date, paid_amount
   - Status: pending/partial/paid (auto-calculated)

2. **Purchase Items Created**
   - One record per item
   - Stores product, unit, quantity, price, total

3. **Inventory Transactions Created**
   - One IN transaction per item
   - Tracks: product, quantity, base_quantity, unit, reference to purchase

---

## 🔍 Example: Create Purchase for Phenol

**Setup:**
- Supplier: "Chemical Co" (id: 1)
- Product: "Phenol" (id: 1)
- Base Unit: "kg"
- Packaging Units:
  - Drum (1 Drum = 250 kg)
  - Bottle (1 Bottle = 1 kg)

**Action:**
1. Go to `/purchase/create`
2. Select "Chemical Co" as supplier
3. Add Item 1:
   - Product: Phenol
   - Unit: Drum (auto-loads)
   - Quantity: 10
   - Price: 5000
   - Row Total: 50,000 (auto-calculated)
4. Add Item 2:
   - Product: Phenol
   - Unit: Bottle (auto-loads)
   - Quantity: 100
   - Price: 500
   - Row Total: 50,000 (auto-calculated)
5. Paid Amount: 0 (will be pending)
6. Click "Create Purchase"

**Result:**
- Purchase created with total: 100,000
- Status: pending (because paid_amount = 0)
- 2 Purchase Items created
- 2 Inventory Transactions created:
  - Transaction 1: +10 Drums (2500 kg base)
  - Transaction 2: +100 Bottles (100 kg base)
- Total inventory increase: 2600 kg

---

## 🎯 View Purchase Details

After creating, you'll be redirected to view page showing:

**Purchase Information:**
- Supplier name
- Purchase date
- Created date

**Payment Status:**
- Total Amount
- Paid Amount
- Outstanding Amount (Total - Paid)
- Status Badge

**Items:**
- Table with product details
- Unit, quantity, price, total for each item

---

## 📱 UI Features

### Dynamic Unit Loading
```
✓ When you select a product, units load automatically
✓ Uses AJAX to fetch matching units
✓ No page refresh needed
✓ Smooth user experience
```

### Auto-Calculations
```
✓ Row totals update instantly as you type
✓ Grand total updates immediately
✓ No manual calculation needed
```

### Add/Remove Items
```
✓ "Add Item" button adds new row
✓ "Remove" button on each row
✓ Grand total recalculates
```

### Status Badges
```
Pending  = Yellow  (not paid)
Partial  = Blue    (partially paid)
Paid     = Green   (fully paid)
```

---

## 🔧 Common Tasks

### Create Purchase with Multiple Items
1. Add first item → Click "Add Item" → Add second item → etc.
2. Fill all fields
3. Review Grand Total
4. Click "Create Purchase"

### Create Partial Payment Purchase
1. Fill in purchase details
2. Set "Paid Amount" to partial amount (e.g., 50% of total)
3. Status will auto-update to "partial"
4. Create purchase

### View Purchase History
1. Go to `/purchase/index`
2. See all purchases sorted by newest first
3. Filter by status (color-coded badges)
4. Click supplier name to jump to supplier details (if integrated)

### Track Inventory Changes
1. After creating purchase
2. Check `inventory_transactions` table
3. See corresponding IN entries with base_quantity calculations

---

## ⚠️ Tips & Reminders

### Before Creating Purchase
```
□ Supplier exists in system
□ Product exists and has units
□ At least one ProductUnit configured
□ Price is correct
□ Quantity is correct
```

### During Creation
```
□ All items have valid data
□ Quantity must be > 0
□ Price must be >= 0
□ Grand total looks correct before submitting
```

### After Creation
```
□ Status updates correctly based on payment
□ Inventory transactions created (verify in table)
□ Base quantities calculated correctly
□ Purchase appears in purchase list
```

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Supplier dropdown empty | Create suppliers in Parties module first |
| Unit dropdown doesn't load | Check product has units defined |
| Item won't remove | Try removing from bottom up |
| Grand total not calculating | Check if item row has quantity and price |
| Purchase won't save | Check all items have valid data |
| Status not updating | Verify paid_amount is correctly set |

---

## 📈 Reports You Can Generate

### From Purchase Data

1. **Purchases by Supplier**
   - What we buy from each supplier
   - Average spend per purchase

2. **Purchase Trends**
   - Purchases over time
   - Spending trends

3. **Outstanding Payables**
   - Partial and pending purchases
   - Amount owed to suppliers

4. **Item Popularity**
   - Most purchased items
   - Supplier-item combinations

---

## 🔗 Links to Other Modules

### Products Module
- Create/manage products at `/product/index`
- Manage units at `/productunit/index?product_id=X`

### Parties Module
- Manage suppliers
- Track supplier contact info

### Inventory Module
- View all transactions (includes purchase IOs)
- Check product stock levels

---

## 📞 Support

For issues or questions:
1. Check PURCHASE_MODULE_README.md for detailed docs
2. Check PURCHASE_API_GUIDE.md for code examples
3. Review PurchaseController for business logic

---

**Quick Links:**
- [Detailed README](PURCHASE_MODULE_README.md)
- [API Guide](PURCHASE_API_GUIDE.md)
- [Product Module Setup](PRODUCT_QUICK_START.md)

---

**System Status:** ✅ READY FOR USE | **Version:** 1.0 | **Date:** March 30, 2026
