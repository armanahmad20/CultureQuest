# Database Troubleshooting Guide

## Problem: "Table 'tblfbr_pct_codes' doesn't exist"

This error occurs because the required database tables haven't been created yet. Here's the solution:

## SOLUTION - 3 Methods

### Method 1: phpMyAdmin (Recommended)
1. **Open phpMyAdmin**
2. **Select your 'fbr' database** from the left sidebar
3. **Click on the 'SQL' tab**
4. **Copy and paste the SQL below** into the text area
5. **Click 'Go' to execute**

### Method 2: Command Line
If you have SSH access to your server:
```bash
mysql -u your_username -p fbr < RUN_THIS_SQL.sql
```

### Method 3: cPanel File Manager
1. Upload the `RUN_THIS_SQL.sql` file to your server
2. Go to cPanel → MySQL Databases
3. Click on phpMyAdmin
4. Select your 'fbr' database
5. Import the SQL file

## EXACT SQL TO RUN

Copy this entire block and paste it into phpMyAdmin:

```sql
-- Make sure you're in the fbr database
USE fbr;

-- Create the missing table
CREATE TABLE IF NOT EXISTS tblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_pct_code (pct_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the double-prefix version too
CREATE TABLE IF NOT EXISTS tbltblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_pct_code (pct_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create store config tables
CREATE TABLE IF NOT EXISTS tblfbr_store_configs (
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
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbltblfbr_store_configs (
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
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create invoice logs tables
CREATE TABLE IF NOT EXISTS tblfbr_invoice_logs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  invoice_id int(11) NOT NULL,
  store_config_id int(11) NOT NULL,
  fbr_invoice_number varchar(100) DEFAULT NULL,
  action varchar(50) NOT NULL,
  request_data text DEFAULT NULL,
  response_data text DEFAULT NULL,
  status varchar(20) NOT NULL,
  error_message text DEFAULT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbltblfbr_invoice_logs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  invoice_id int(11) NOT NULL,
  store_config_id int(11) NOT NULL,
  fbr_invoice_number varchar(100) DEFAULT NULL,
  action varchar(50) NOT NULL,
  request_data text DEFAULT NULL,
  response_data text DEFAULT NULL,
  status varchar(20) NOT NULL,
  error_message text DEFAULT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert required data
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

INSERT IGNORE INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT IGNORE INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

-- Check that tables were created
SHOW TABLES LIKE '%fbr%';
SELECT 'SUCCESS: All tables created!' as result;
```

## After Running the SQL

1. **Verify tables exist** by running:
   ```sql
   SHOW TABLES LIKE '%fbr%';
   ```

2. **You should see these tables**:
   - `tblfbr_pct_codes`
   - `tbltblfbr_pct_codes`
   - `tblfbr_store_configs`
   - `tbltblfbr_store_configs`
   - `tblfbr_invoice_logs`
   - `tbltblfbr_invoice_logs`

3. **Go to Perfex CRM** → Setup → Modules → Activate "FBR POS Integration"

## Still Having Issues?

If you still get errors:

1. **Check database permissions**: Make sure your database user has CREATE TABLE permissions
2. **Check database name**: Confirm you're running the SQL in the "fbr" database
3. **Check for typos**: Make sure you copied the entire SQL block

## Common Issues

- **"Access denied"**: Your database user doesn't have CREATE permissions
- **"Database doesn't exist"**: Make sure you have a database named "fbr"
- **"Syntax error"**: Make sure you copied the entire SQL block without truncation

## Success Indicators

After running the SQL successfully, you should be able to:
1. Activate the FBR POS Integration module without errors
2. See the FBR dashboard in your Perfex CRM
3. Configure store settings
4. Manage PCT codes