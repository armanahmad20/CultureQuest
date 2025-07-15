-- FBR POS Integration Database Schema Fix
-- This script fixes the table naming issue and ensures correct table structure

-- First, drop any incorrectly named tables
DROP TABLE IF EXISTS `tbltblfbr_store_configs`;
DROP TABLE IF EXISTS `tbltblfbr_invoice_logs`;
DROP TABLE IF EXISTS `tbltblfbr_pct_codes`;

-- Create the correct table structures
CREATE TABLE IF NOT EXISTS `tblfbr_store_configs` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `store_name` varchar(255) NOT NULL,
    `store_id` varchar(50) NOT NULL,
    `ntn` varchar(15) NOT NULL,
    `strn` varchar(15) NOT NULL,
    `address` text NOT NULL,
    `pos_type` varchar(100) NOT NULL,
    `pos_version` varchar(20) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `sdc_url` varchar(255) DEFAULT 'http://localhost:8080',
    `sdc_username` varchar(100) DEFAULT NULL,
    `sdc_password` varchar(100) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tblfbr_invoice_logs` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `invoice_id` int(11) NOT NULL,
    `fbr_invoice_number` varchar(50) DEFAULT NULL,
    `request_data` text NOT NULL,
    `response_data` text DEFAULT NULL,
    `status` enum('pending','confirmed','failed') DEFAULT 'pending',
    `error_message` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `invoice_id` (`invoice_id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tblfbr_pct_codes` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `pct_code` varchar(20) NOT NULL,
    `description` text NOT NULL,
    `tax_rate` decimal(5,2) DEFAULT 17.00,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_pct_code` (`pct_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default store configuration
INSERT IGNORE INTO `tblfbr_store_configs` (`store_name`, `store_id`, `ntn`, `strn`, `address`, `pos_type`, `pos_version`, `ip_address`, `sdc_url`, `is_active`, `created_at`) 
VALUES ('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 'http://localhost:8080', 1, NOW());

-- Insert sample PCT codes
INSERT IGNORE INTO `tblfbr_pct_codes` (`pct_code`, `description`, `tax_rate`, `is_active`, `created_at`) VALUES 
('01111000', 'Food and Beverages', 17.00, 1, NOW()),
('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
('05111000', 'Automotive and Parts', 17.00, 1, NOW());