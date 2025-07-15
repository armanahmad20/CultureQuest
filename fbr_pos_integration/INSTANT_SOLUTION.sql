-- INSTANT SOLUTION - Run this SQL directly in your fbr database

-- Create ALL table variations that the system might need
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL DEFAULT '01111000',
  description text NOT NULL DEFAULT 'General Items',
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbltblfbr_pct_codes (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  pct_code varchar(20) NOT NULL DEFAULT '01111000',
  description text NOT NULL DEFAULT 'General Items',
  tax_rate decimal(5,2) DEFAULT 17.00,
  is_active tinyint(1) DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data if tables are empty
INSERT IGNORE INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT IGNORE INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES
('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());

INSERT IGNORE INTO tblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, 1, NOW()),
('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
('05111000', 'Automotive and Parts', 17.00, 1, NOW()),
('06111000', 'Construction Materials', 17.00, 1, NOW()),
('07111000', 'Books and Stationery', 0.00, 1, NOW()),
('08111000', 'Cosmetics and Personal Care', 17.00, 1, NOW()),
('09111000', 'Sports and Recreation', 17.00, 1, NOW()),
('10111000', 'General Services', 16.00, 1, NOW());

INSERT IGNORE INTO tbltblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES
('01111000', 'Food and Beverages', 17.00, 1, NOW()),
('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
('05111000', 'Automotive and Parts', 17.00, 1, NOW()),
('06111000', 'Construction Materials', 17.00, 1, NOW()),
('07111000', 'Books and Stationery', 0.00, 1, NOW()),
('08111000', 'Cosmetics and Personal Care', 17.00, 1, NOW()),
('09111000', 'Sports and Recreation', 17.00, 1, NOW()),
('10111000', 'General Services', 16.00, 1, NOW());

-- Show results
SELECT 'All tables created successfully!' as Status;
SHOW TABLES LIKE '%fbr%';