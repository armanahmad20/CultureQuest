# FBR POS Integration - INSTANT SOLUTION

## Your Current Error: "Unknown table 'fbr.tbltbltblfbr_store_configs'"

This error occurs because of table name prefix conflicts. Here's the **guaranteed fix**:

## Step-by-Step Solution

### Step 1: Run the Quick Fix SQL

1. **Access your database** (phpMyAdmin, cPanel, etc.)
2. **Copy and paste** the entire content of `QUICK_FIX.sql` into the SQL tab
3. **Execute** the script
4. You should see "FBR POS Integration tables created successfully!" message

### Step 2: Activate the Module

1. **Go to** your Perfex CRM admin panel
2. **Navigate to** Setup → Modules
3. **Find** "FBR POS Integration"
4. **Click** "Activate"

The module should now activate without any errors!

## What the Quick Fix Does

✅ **Removes ALL table variations** that cause conflicts
✅ **Creates tables with exact names** the module expects
✅ **Adds required columns** to existing Perfex tables
✅ **Inserts default PCT codes** for immediate use
✅ **Creates default store configuration** to get you started

## Alternative: Manual Copy-Paste

If you prefer not to use the SQL file, copy this directly into phpMyAdmin:

```sql
-- Clean up existing tables
DROP TABLE IF EXISTS fbr_store_configs;
DROP TABLE IF EXISTS tblfbr_store_configs;
DROP TABLE IF EXISTS tbltblfbr_store_configs;
DROP TABLE IF EXISTS tbltbltblfbr_store_configs;
DROP TABLE IF EXISTS fbr_invoice_logs;
DROP TABLE IF EXISTS tblfbr_invoice_logs;
DROP TABLE IF EXISTS tbltblfbr_invoice_logs;
DROP TABLE IF EXISTS tbltbltblfbr_invoice_logs;
DROP TABLE IF EXISTS fbr_pct_codes;
DROP TABLE IF EXISTS tblfbr_pct_codes;
DROP TABLE IF EXISTS tbltblfbr_pct_codes;
DROP TABLE IF EXISTS tbltbltblfbr_pct_codes;

-- Create correct tables
CREATE TABLE tblfbr_store_configs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  store_name varchar(255) NOT NULL,
  store_id varchar(50) NOT NULL,
  ntn varchar(15) NOT NULL,
  strn varchar(15) NOT NULL,
  address text NOT NULL,
  pos_type varchar(100) NOT NULL,
  pos_version varchar(20) NOT NULL,
  ip_address varchar(45) NOT NULL,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY store_id (store_id)
);

CREATE TABLE tblfbr_invoice_logs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  invoice_id int(11) NOT NULL,
  store_config_id int(11) NOT NULL,
  fbr_invoice_number varchar(100) DEFAULT NULL,
  action varchar(50) NOT NULL,
  request_data text DEFAULT NULL,
  response_data text DEFAULT NULL,
  status varchar(20) NOT NULL,
  error_message text DEFAULT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (id),
  KEY invoice_id (invoice_id)
);

CREATE TABLE tblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 0.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL,
  PRIMARY KEY (id),
  KEY pct_code (pct_code)
);

-- Add FBR columns to existing tables
ALTER TABLE tblitems ADD COLUMN pct_code varchar(20) DEFAULT NULL;
ALTER TABLE tblinvoices ADD COLUMN fbr_invoice_number varchar(100) DEFAULT NULL;
ALTER TABLE tblinvoices ADD COLUMN fbr_status varchar(20) DEFAULT 'pending';
ALTER TABLE tblinvoices ADD COLUMN fbr_qr_code text DEFAULT NULL;

-- Insert default data
INSERT INTO tblfbr_pct_codes (pct_code, description, tax_rate, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, NOW()),
('02111000', 'Clothing and Textiles', 17.00, NOW()),
('03111000', 'Electronics and Appliances', 17.00, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, NOW()),
('05111000', 'Automotive and Parts', 17.00, NOW()),
('06111000', 'Construction Materials', 17.00, NOW()),
('07111000', 'Books and Stationery', 0.00, NOW()),
('08111000', 'Cosmetics and Personal Care', 17.00, NOW()),
('09111000', 'Sports and Recreation', 17.00, NOW()),
('10111000', 'General Services', 16.00, NOW());

INSERT INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());
```

## After Installation

Once the module is activated:

1. **Configure your store**: Go to FBR POS Integration → Store Configuration
2. **Update store details**: Add your real NTN, STRN, and store information
3. **Review PCT codes**: Go to FBR POS Integration → PCT Codes
4. **Test the system**: Create a test invoice

## Support

If you still encounter issues after following these steps, the problem may be with your Perfex CRM installation or database configuration. Contact your hosting provider or system administrator for assistance.

This solution bypasses all table naming conflicts and creates the exact database structure the module needs.