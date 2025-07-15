# DIRECT SOLUTION for Database Schema Issue

## The Problem
The error `'fbr.tbltblfbr_store_configs'` shows the system is looking for a table in a database schema called 'fbr' rather than your actual database.

## Quick Fix Steps

### Step 1: Check Your Database Name
1. **Go to phpMyAdmin**
2. **Look at the left sidebar** - what is your database name?
3. **Make sure you're in the correct database** (usually something like `perfex_crm` or your domain name)

### Step 2: Run This Simple SQL
Replace `YOUR_DATABASE_NAME` with your actual database name:

```sql
-- Use your actual database
USE YOUR_DATABASE_NAME;

-- Create the exact tables the system is looking for
CREATE TABLE tbltblfbr_store_configs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  store_name varchar(255) NOT NULL DEFAULT 'Default Store',
  store_id varchar(50) NOT NULL DEFAULT 'STORE001',
  ntn varchar(15) NOT NULL DEFAULT '0000000000000',
  strn varchar(15) NOT NULL DEFAULT 'STRN000000',
  address text NOT NULL DEFAULT 'Default Address',
  pos_type varchar(100) NOT NULL DEFAULT 'Perfex CRM',
  pos_version varchar(20) NOT NULL DEFAULT '1.0.0',
  ip_address varchar(45) NOT NULL DEFAULT '127.0.0.1',
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE tbltblfbr_invoice_logs (
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

CREATE TABLE tbltblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL DEFAULT '01111000',
  description text NOT NULL DEFAULT 'General Items',
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

-- Insert minimum required data
INSERT INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT INTO tbltblfbr_pct_codes (pct_code, description, tax_rate, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, NOW()),
('02111000', 'Clothing and Textiles', 17.00, NOW()),
('03111000', 'Electronics and Appliances', 17.00, NOW());
```

### Step 3: Alternative - Copy-Paste Method
If you don't know your database name:

1. **In phpMyAdmin**, look at the top of the page - it shows your database name
2. **OR** run this query first: `SELECT DATABASE();`
3. **Then use that name** in the SQL above

### Step 4: Verify Tables Exist
Run this to check if tables were created:
```sql
SHOW TABLES LIKE '%fbr%';
```

You should see:
- `tbltblfbr_store_configs`
- `tbltblfbr_invoice_logs`
- `tbltblfbr_pct_codes`

### Step 5: Activate Module
Once tables exist, go to **Setup → Modules** and activate **FBR POS Integration**.

## Why This Works
- Creates tables with the exact names the error is looking for
- Uses your actual database schema, not a fake 'fbr' schema
- Includes minimum required data to prevent empty table errors

## Still Not Working?
If you still get the error:
1. **Check your Perfex CRM database configuration** in `application/config/database.php`
2. **Verify your database user has proper permissions**
3. **Try deactivating and reactivating the module** after creating the tables