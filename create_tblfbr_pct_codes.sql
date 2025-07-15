-- SQL to create tblfbr_pct_codes table
-- Copy and paste this into phpMyAdmin SQL tab

CREATE TABLE `tblfbr_pct_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pct_code` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT 17.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_pct_code` (`pct_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample PCT codes
INSERT INTO `tblfbr_pct_codes` (`pct_code`, `description`, `tax_rate`, `is_active`, `created_at`) VALUES
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