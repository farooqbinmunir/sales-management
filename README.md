# Sales Management Plugin
## by Farooq Bin Munir, Akram Bin Waris

Document Version: 1.0  
Prepared On: February 28, 2026  
Audience: Store Owners, Cashiers/Salesmen, Administrators, QA/Client Review Team

---

## 1. Purpose of This Document
This document is designed for two goals:
1. Help your client use the plugin correctly in daily operations.
2. Provide a full step-by-step testing guide to verify each feature before go-live.

---

## 2. Plugin Features Covered
This guide covers all major plugin pages and flows:
1. Sales Entry
2. Sales Listing and Today Summary
3. Products and Manufacturers
4. Purchases
5. Pending Payments (Sale Recoveries + Purchase Payments)
6. Customers
7. Returns
8. Stock
9. Invoice Details (Sale and Purchase)
10. Analytics
11. Salesman Authentication (User + Pincode)
12. Receipt/Print output validation

---

## 3. Roles and Access
Recommended operational roles:
1. Administrator
- Configure plugin and users.
- Set sales pincode for each salesman.
- Manage all pages.

2. Salesman/Cashier
- Create sales entries.
- Authenticate in popup with selected user + pincode.
- Print and save bills.

3. Accountant/Manager
- Review sales, purchases, pending payments, customers, and analytics.

Important:
- Sales authentication uses a dedicated pincode field in WordPress User Profile.
- It is separate from WordPress login password.

---

## 4. Pre-Setup Checklist (Before Testing or Go-Live)
Complete this once:
1. Activate plugin in WordPress.
2. Confirm plugin menu is visible in admin.
3. Go to `Users > Edit User` for each salesperson.
4. Set `Sales Pincode` (4 to 10 digits), then save.
5. Add at least 3 manufacturers.
6. Add at least 5 products (different vendors/manufacturers).
7. Add opening stock via Purchase page (paid and unpaid examples).
8. Confirm Pending Payments page shows dues for unpaid/partial purchases.

---

## 5. Daily Operational Flow (Recommended)
Use this exact order daily:
1. Record Purchases to update stock.
2. Perform Sales (Cash, Credit, Partially Paid) through Sales Entry.
3. Receive/pay due amounts from Pending Payments.
4. Process Returns if needed.
5. Review Sales page Today Summary and Cash In Hand.
6. Verify physical cash against `Total Cash In Hand`.

---

## 6. Feature Usage Guide

### 6.1 Products Page
Use for product and manufacturer master data.

Steps:
1. Open `Products` page.
2. Add manufacturer from Manufacturers panel.
3. Add product with:
- Name
- Purchase Price
- Sale Price
- Min Quantity
- Vendor
- Manufacturer
- Location
4. Use quick edit to update product details.

Expected behavior:
1. Product appears in listing.
2. Manufacturer mapping is visible.
3. Stock starts from 0 until purchased.

---

### 6.2 Purchase Page
Use for stock entry and vendor payment tracking.

Steps:
1. Open `Purchase` page.
2. Click `Add New Purchase`.
3. Add one or more products.
4. Set payment status:
- Paid
- Unpaid
- Partially Paid
5. Enter vendor and optional description.
6. Save purchase.

Expected behavior:
1. Purchase row appears with `Payment`, `Paid`, and `Remaining` columns.
2. Stock increases.
3. For unpaid/partial purchases, due is created in Pending Payments.
4. Purchase invoice details are printable and linked from Pending Payments.

---

### 6.3 Sales Entry Page
Use to generate and save sales with receipt printing.

Steps:
1. Open `Sales Management` main page.
2. Add products to selected list.
3. Enter quantity and optional discount.
4. Select Sales Type:
- Cash Sale
- Credit Sale
- Partially Paid
5. For Credit/Partial:
- Fill customer name and phone in popup.
6. Click `Print and Save`.
7. In auth popup:
- Select salesman from dropdown.
- Enter pincode.
- Confirm.

Expected behavior:
1. Customer form validates required fields before auth popup.
2. Customer popup closes before auth popup opens.
3. Wrong pincode shows inline error (not browser alert).
4. Sale is saved only after successful auth.
5. Receipt includes correct financial and account status details.
6. Credit/partial sale creates due entry and appears in Pending Payments.

---

### 6.4 Sales Listing + Today Summary
Open `Sales` page.

Today Summary includes:
1. Today's Sale
2. Today's Purchase
3. Today's Profit
4. Today's Discount
5. Today's Recovery
6. Today's Payments (Paid)
7. Total Cash In Hand

Formula:
`Cash In Hand = (Cash Sale + Initial Received + Recovery) - (Initial Purchase Paid + Payments Paid)`

Use this value to match physical cash.

---

### 6.5 Pending Payments Page
Central page for dues.

Capabilities:
1. Filter by status: Open / Closed / All
2. Filter by due type: Sale / Purchase
3. Separate `Customer` and `Vendor` columns
4. Invoice column links directly to affected Sale/Purchase invoice details
5. Action button changes by due type:
- Sale due: `Receive Payment`
- Purchase due: `Pay Payment`

Expected behavior:
1. Payment history updates.
2. Remaining amount decreases.
3. Status closes at zero remaining.
4. Related sale/purchase master record is updated.

---

### 6.6 Customers Page
Open `Customers` page.

Shows:
1. Customer profile fields
2. Total sales count
3. Total sales amount
4. Open due amount
5. Last invoice (linked)

Important logic:
- Credit/partial sale customer matching is based on phone number.
- Existing customer is reused if phone already exists.

---

### 6.7 Returns Page
Use for product return against invoice.

Steps:
1. Enter invoice number.
2. Select invoiced product.
3. Enter return quantity/reason.
4. Submit return.

Expected behavior:
1. Return appears in returns listing.
2. Stock is adjusted back.
3. Return amount and date are recorded.

---

### 6.8 Stock Page
Shows current stock valuation.

Outputs include:
1. Quantity
2. Purchase/Sale rates
3. Purchase amount
4. Sale amount
5. Profit per product
6. Totals in footer

Expected behavior:
- Low stock and zero stock rows are visibly highlighted.

---

### 6.9 Invoice Detail Pages (Sale and Purchase)
Use linked invoice pages for audit and print.

Expected sale invoice fields:
1. Customer Name
2. Invoice No
3. Item table
4. Total items, gross, discount, net
5. Sales person
6. Paid amount
7. Payment history
8. Remaining amount
9. Account status
10. Payment status
11. Sale type
12. Payment method (when applicable)

Expected purchase invoice fields:
1. Salesman
2. Invoice No
3. Invoice type
4. Vendor
5. Item table
6. Total payment
7. Paid amount
8. Payment history
9. Remaining amount
10. Account status
11. Payment status
12. Payment method (when applicable)

---

### 6.10 Analytics Page
Use for management reporting:
1. Top selling products
2. Most profitable products
3. Low stock alerts
4. Recent sales
5. Monthly sales chart
6. Product performance chart

---

## 7. Full End-to-End Testing Guide
Run this in a fresh test cycle and capture screenshots.

### 7.1 Access and Security Tests

Test A01: Admin access
1. Login as admin.
2. Open each plugin submenu page.
Expected: All pages load without permission errors.

Test A02: Sales pincode field
1. Open `Users > Edit User`.
2. Set pincode and save.
3. Reload page.
Expected:
1. Pincode input stays blank (security).
2. Status shows `Set`.
3. Last updated timestamp is visible.

Test A03: Clear pincode
1. Tick `Clear current pincode` and save.
Expected: Status becomes `Not set`.

---

### 7.2 Products and Manufacturer Tests

Test P01: Add manufacturer
1. Add new manufacturer.
Expected: New manufacturer appears in list.

Test P02: Duplicate manufacturer
1. Add same name again.
Expected: Duplicate prevention message appears.

Test P03: Add product
1. Add product with valid values.
Expected: Product appears in products table.

Test P04: Edit product
1. Quick edit product name/rates/location.
Expected: Updated values save and display.

---

### 7.3 Purchase Tests

Test PU01: Paid purchase
1. Create purchase with `Paid` status.
Expected:
1. Purchase listing shows paid amount = total, remaining = 0.
2. No open due in Pending Payments.

Test PU02: Unpaid purchase
1. Create purchase with `Unpaid` status.
Expected:
1. Due appears in Pending Payments as Purchase type.
2. Vendor column is populated.
3. Invoice link opens purchase invoice details page.

Test PU03: Partial purchase
1. Create purchase with partial paid amount.
Expected:
1. Remaining > 0 in purchase listing.
2. Due is open in Pending Payments.

---

### 7.4 Sales Entry Tests

Test S01: Cash sale
1. Add products and quantities.
2. Sales Type = Cash Sale.
3. Print and Save with auth success.
Expected:
1. Sale saved.
2. No pending due created.
3. Receipt shows paid as full and remaining 0.

Test S02: Credit sale validation
1. Sales Type = Credit Sale.
2. Click Print and Save without customer name/phone.
Expected:
1. Inline validation errors on required customer fields.
2. Auth popup does not open.

Test S03: Credit sale save flow
1. Fill customer name + phone.
2. Print and Save.
3. In auth popup select user + correct pincode.
Expected:
1. Customer popup closes before auth popup.
2. Sale saves successfully.
3. Due entry appears in Pending Payments.

Test S04: Partially paid sale
1. Sales Type = Partially Paid.
2. Enter paid amount less than net total.
3. Complete auth.
Expected:
1. Due amount calculated correctly.
2. Pending Payments entry is created.

Test S05: Invalid paid amount
1. Enter paid amount > net total.
Expected: Validation blocks save.

Test S06: Customer dedup by phone
1. Create credit sale for phone `03XXXXXXXXX`.
2. Create another credit sale with same phone and different name.
Expected: Existing customer record is reused by phone.

Test S07: Wrong pincode
1. Enter wrong pincode in auth popup.
Expected: Error message appears below pincode field.

Test S08: Salesman attribution
1. Save sales using different selected users in auth popup.
Expected: Sales listing and receipts show correct salesperson attribution.

---

### 7.5 Pending Payments Tests

Test D01: Receive sale recovery
1. Open open Sale due.
2. Click Receive Payment.
3. Submit partial amount.
Expected:
1. Paid/Remaining update.
2. Payment history records date, note, amount.
3. Sale payment status updates.

Test D02: Pay vendor payment
1. Open open Purchase due.
2. Click Pay Payment.
3. Submit amount.
Expected:
1. Paid/Remaining update.
2. Purchase table paid/remaining/status update.

Test D03: Full settlement
1. Pay/receive exact remaining amount.
Expected: Status becomes Closed and action button is disabled.

Test D04: Invoice link integrity
1. Click invoice from sale due and purchase due rows.
Expected: Correct detail page opens for affected record.

---

### 7.6 Receipt and Invoice Print Tests

Test R01: Sale invoice print data
1. Open sale invoice detail and print.
Expected printed output includes:
1. Customer name
2. Invoice number
3. Sales person
4. Items and amounts
5. Gross, discount, net
6. Paid amount
7. Remaining amount
8. Account status
9. Payment status
10. Sale type and method

Test R02: Purchase invoice print data
1. Open purchase invoice detail and print.
Expected printed output includes:
1. Vendor
2. Salesman
3. Invoice number
4. Item table
5. Total paid/remaining
6. Account status and payment status
7. Payment method when applicable

Test R03: Number formatting
1. Test with amounts above 1,000.
Expected: Calculations and printed due/paid values stay correct.

---

### 7.7 Customers, Returns, Stock, Analytics Tests

Test C01: Customers summary
1. Open Customers page.
Expected: Sales amount/open due/last invoice are correct.

Test RT01: Return processing
1. Process return for valid invoice item.
Expected: Return row appears and stock is increased.

Test ST01: Stock valuation
1. Open Stock page after multiple purchases/sales/returns.
Expected: Quantities and valuation are consistent.

Test AN01: Analytics widgets
1. Open Analytics.
Expected: Cards and charts render without JS/PHP errors.

---

## 8. UAT Sign-Off Template
Use this with your client:

1. Environment
- URL:
- WordPress Version:
- Plugin Version:
- Test Date:

2. Result Summary
- Total test cases executed:
- Passed:
- Failed:
- Blocked:

3. Sign-Off
- Client Name:
- Client Signature:
- Date:

---

## 9. Troubleshooting Quick Notes
1. Auth popup rejects valid user
- Confirm pincode is set for selected user profile.

2. Credit sale not appearing in Pending Payments
- Verify due amount > 0 and sale saved successfully.

3. Invoice link not opening expected data
- Recheck whether due type is sale vs purchase and linked invoice number.

4. Summary cash mismatch
- Reconcile with formula shown on Sales page and ensure all pending payments were entered on same date.

---

## 10. Go-Live Checklist
1. All critical tests in Section 7 passed.
2. At least one full end-to-end day simulation completed.
3. User pincodes configured for all salesmen.
4. Opening stock validated.
5. Client trained on Daily Operational Flow.
6. Backup policy confirmed.

---

## 11. Support Notes
For support tickets, always include:
1. Page name
2. Invoice number (if applicable)
3. Due ID (if pending payment issue)
4. Screenshot/video
5. Exact steps and expected vs actual result
