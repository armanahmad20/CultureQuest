-- SAFE FIX for FBR POS Integration Database Issues
-- This version handles existing columns and tables safely

-- Clean up ALL possible table variations
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

-- Create the correct tables
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 0.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL,
  PRIMARY KEY (id),
  KEY pct_code (pct_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add FBR columns to existing tables ONLY if they don't exist
-- Check and add pct_code column to tblitems
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'tblitems' 
                   AND COLUMN_NAME = 'pct_code');

SET @sql = IF(@col_exists = 0, 'ALTER TABLE tblitems ADD COLUMN pct_code varchar(20) DEFAULT NULL', 'SELECT "pct_code column already exists in tblitems"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add fbr_invoice_number column to tblinvoices
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'tblinvoices' 
                   AND COLUMN_NAME = 'fbr_invoice_number');

SET @sql = IF(@col_exists = 0, 'ALTER TABLE tblinvoices ADD COLUMN fbr_invoice_number varchar(100) DEFAULT NULL', 'SELECT "fbr_invoice_number column already exists in tblinvoices"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add fbr_status column to tblinvoices
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'tblinvoices' 
                   AND COLUMN_NAME = 'fbr_status');

SET @sql = IF(@col_exists = 0, 'ALTER TABLE tblinvoices ADD COLUMN fbr_status varchar(20) DEFAULT "pending"', 'SELECT "fbr_status column already exists in tblinvoices"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add fbr_qr_code column to tblinvoices
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'tblinvoices' 
                   AND COLUMN_NAME = 'fbr_qr_code');

SET @sql = IF(@col_exists = 0, 'ALTER TABLE tblinvoices ADD COLUMN fbr_qr_code text DEFAULT NULL', 'SELECT "fbr_qr_code column already exists in tblinvoices"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Clear existing PCT codes to avoid duplicates
DELETE FROM tblfbr_pct_codes;

-- Insert default PCT codes
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

-- Clear existing store configs to avoid duplicates
DELETE FROM tblfbr_store_configs;

-- Insert default store configuration
INSERT INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

-- Success message
SELECT 'FBR POS Integration database setup completed successfully!' as Status;
SELECT 'You can now activate the module in Setup > Modules' as NextStep;