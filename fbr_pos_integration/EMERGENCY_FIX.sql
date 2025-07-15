-- EMERGENCY FIX for Database Schema Issue
-- The error shows 'fbr.tbltblfbr_store_configs' which means it's looking in wrong database

-- First, check what database we're actually in
SELECT DATABASE() as current_database;

-- Show all databases
SHOW DATABASES;

-- Create tables in the CURRENT database (not a specific schema)
-- Clean up first
DROP TABLE IF EXISTS tblfbr_store_configs;
DROP TABLE IF EXISTS tbltblfbr_store_configs;
DROP TABLE IF EXISTS tblfbr_invoice_logs;
DROP TABLE IF EXISTS tbltblfbr_invoice_logs;
DROP TABLE IF EXISTS tblfbr_pct_codes;
DROP TABLE IF EXISTS tbltblfbr_pct_codes;

-- Create with explicit database reference
SET @db_name = DATABASE();
SET @sql = CONCAT('CREATE TABLE ', @db_name, '.tblfbr_store_configs (
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
  PRIMARY KEY (id)
)');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @db_name, '.tbltblfbr_store_configs (
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
  PRIMARY KEY (id)
)');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @db_name, '.tblfbr_invoice_logs (
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
  PRIMARY KEY (id)
)');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @db_name, '.tbltblfbr_invoice_logs (
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
  PRIMARY KEY (id)
)');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @db_name, '.tblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 0.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL,
  PRIMARY KEY (id)
)');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @db_name, '.tbltblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL,
  description text NOT NULL,
  tax_rate decimal(5,2) DEFAULT 0.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL,
  PRIMARY KEY (id)
)');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Insert data using current database
INSERT INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

-- Insert PCT codes
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

INSERT INTO tbltblfbr_pct_codes (pct_code, description, tax_rate, created_at) VALUES
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

-- Show final status
SELECT 'Emergency fix completed!' as Status;
SELECT DATABASE() as 'Tables created in database';
SHOW TABLES LIKE '%fbr%';