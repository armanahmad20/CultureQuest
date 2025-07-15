-- FINAL SQL for FBR Database
-- Since your database is named "fbr", this SQL will create the exact tables the system needs

-- Clean up any existing tables first
DROP TABLE IF EXISTS tbltblfbr_store_configs;
DROP TABLE IF EXISTS tbltblfbr_invoice_logs;
DROP TABLE IF EXISTS tbltblfbr_pct_codes;

-- Create the exact table the system is looking for: tbltblfbr_store_configs
CREATE TABLE tbltblfbr_store_configs (
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
  UNIQUE KEY unique_store_id (store_id),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create the invoice logs table
CREATE TABLE tbltblfbr_invoice_logs (
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
  INDEX idx_invoice_id (invoice_id),
  INDEX idx_status (status),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create the PCT codes table
CREATE TABLE tbltblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 0.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY unique_pct_code (pct_code),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert required store configuration data
INSERT INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

-- Insert PCT codes with Pakistani tax categories
INSERT INTO tbltblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, 1, NOW()),
('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
('05111000', 'Automotive and Parts', 17.00, 1, NOW()),
('06111000', 'Construction Materials', 17.00, 1, NOW()),
('07111000', 'Books and Stationery', 0.00, 1, NOW()),
('08111000', 'Cosmetics and Personal Care', 17.00, 1, NOW()),
('09111000', 'Sports and Recreation', 17.00, 1, NOW()),
('10111000', 'General Services', 16.00, 1, NOW()),
('11111000', 'Furniture and Home Decor', 17.00, 1, NOW()),
('12111000', 'Jewelry and Accessories', 17.00, 1, NOW()),
('13111000', 'Agricultural Products', 0.00, 1, NOW()),
('14111000', 'Industrial Equipment', 17.00, 1, NOW()),
('15111000', 'Professional Services', 16.00, 1, NOW());

-- Verify tables were created successfully
SELECT 'Tables created successfully!' as Status;

-- Show the tables that were created
SHOW TABLES LIKE 'tbltblfbr%';

-- Count records in each table
SELECT 'Store Configs' as TableName, COUNT(*) as RecordCount FROM tbltblfbr_store_configs
UNION ALL
SELECT 'PCT Codes' as TableName, COUNT(*) as RecordCount FROM tbltblfbr_pct_codes
UNION ALL
SELECT 'Invoice Logs' as TableName, COUNT(*) as RecordCount FROM tbltblfbr_invoice_logs;