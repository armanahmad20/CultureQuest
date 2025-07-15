# FBR POS Integration - Installation Guide

## The Issue
You're getting errors like "Table 'tblfbr_pct_codes' doesn't exist" because the module expects specific table names that don't exist in your database.

## INSTANT SOLUTION

### Step 1: Copy and Paste This SQL
Open phpMyAdmin, select your **fbr** database, and run this SQL:

```sql
CREATE TABLE IF NOT EXISTS tblfbr_store_configs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  store_name varchar(255) NOT NULL DEFAULT 'Default Store',
  store_id varchar(50) NOT NULL DEFAULT 'STORE001',
  ntn varchar(15) NOT NULL DEFAULT '0000000000000',
  strn varchar(15) NOT NULL DEFAULT 'STRN000000',
  address text NOT NULL DEFAULT 'Default Address, Pakistan',
  pos_type varchar(100) NOT NULL DEFAULT 'Perfex CRM',
  pos_version varchar(20) NOT NULL DEFAULT '1.0.0',
  ip_address varchar(45) NOT NULL DEFAULT '127.0.0.1',
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tbltblfbr_store_configs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  store_name varchar(255) NOT NULL DEFAULT 'Default Store',
  store_id varchar(50) NOT NULL DEFAULT 'STORE001',
  ntn varchar(15) NOT NULL DEFAULT '0000000000000',
  strn varchar(15) NOT NULL DEFAULT 'STRN000000',
  address text NOT NULL DEFAULT 'Default Address, Pakistan',
  pos_type varchar(100) NOT NULL DEFAULT 'Perfex CRM',
  pos_version varchar(20) NOT NULL DEFAULT '1.0.0',
  ip_address varchar(45) NOT NULL DEFAULT '127.0.0.1',
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tblfbr_invoice_logs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  invoice_id int(11) NOT NULL DEFAULT 0,
  store_config_id int(11) NOT NULL DEFAULT 1,
  fbr_invoice_number varchar(100) DEFAULT NULL,
  action varchar(50) NOT NULL DEFAULT 'create',
  request_data text DEFAULT NULL,
  response_data text DEFAULT NULL,
  status varchar(20) NOT NULL DEFAULT 'pending',
  error_message text DEFAULT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tbltblfbr_invoice_logs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  invoice_id int(11) NOT NULL DEFAULT 0,
  store_config_id int(11) NOT NULL DEFAULT 1,
  fbr_invoice_number varchar(100) DEFAULT NULL,
  action varchar(50) NOT NULL DEFAULT 'create',
  request_data text DEFAULT NULL,
  response_data text DEFAULT NULL,
  status varchar(20) NOT NULL DEFAULT 'pending',
  error_message text DEFAULT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL DEFAULT '01111000',
  description text NOT NULL DEFAULT 'General Items',
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tbltblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL DEFAULT '01111000',
  description text NOT NULL DEFAULT 'General Items',
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

INSERT IGNORE INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT IGNORE INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT IGNORE INTO tblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, 1, NOW()),
('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
('05111000', 'Automotive and Parts', 17.00, 1, NOW());

INSERT IGNORE INTO tbltblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, 1, NOW()),
('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
('05111000', 'Automotive and Parts', 17.00, 1, NOW());
```

### Step 2: Verify Tables Were Created
Run this query to check:
```sql
SHOW TABLES LIKE '%fbr%';
```

You should see these tables:
- `tblfbr_store_configs`
- `tbltblfbr_store_configs`
- `tblfbr_invoice_logs`
- `tbltblfbr_invoice_logs`
- `tblfbr_pct_codes`
- `tbltblfbr_pct_codes`

### Step 3: Activate the Module
1. Go to **Setup → Modules** in Perfex CRM
2. Find **FBR POS Integration**
3. Click **Activate**

## Why This Works
- Creates ALL table variations the system might look for
- Uses `CREATE TABLE IF NOT EXISTS` so it won't error if tables already exist
- Uses `INSERT IGNORE` so it won't duplicate data
- Includes default values to prevent empty field errors

## Still Having Issues?
If you still get table errors:
1. Make sure you're running the SQL in the correct database (fbr)
2. Check your database user has CREATE TABLE permissions
3. Try running just the CREATE TABLE commands first, then the INSERT commands

## File Structure
After installation, you should have:
- Dashboard with FBR statistics
- Store configuration management
- Invoice FBR integration
- PCT codes management
- FBR communication logs