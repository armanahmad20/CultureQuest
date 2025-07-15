-- FBR POS Integration - Database Cleanup Script
-- Run this BEFORE activating the module to avoid table conflicts

-- Drop all possible table name variations
DROP TABLE IF EXISTS `fbr_store_configs`;
DROP TABLE IF EXISTS `fbr_invoice_logs`;
DROP TABLE IF EXISTS `fbr_pct_codes`;

DROP TABLE IF EXISTS `tblfbr_store_configs`;
DROP TABLE IF EXISTS `tblfbr_invoice_logs`;
DROP TABLE IF EXISTS `tblfbr_pct_codes`;

DROP TABLE IF EXISTS `tbltblfbr_store_configs`;
DROP TABLE IF EXISTS `tbltblfbr_invoice_logs`;
DROP TABLE IF EXISTS `tbltblfbr_pct_codes`;

DROP TABLE IF EXISTS `tbltbltblfbr_store_configs`;
DROP TABLE IF EXISTS `tbltbltblfbr_invoice_logs`;
DROP TABLE IF EXISTS `tbltbltblfbr_pct_codes`;

-- Remove any FBR-related columns that might have been added to existing tables
ALTER TABLE `tblitems` DROP COLUMN IF EXISTS `pct_code`;
ALTER TABLE `tblinvoices` DROP COLUMN IF EXISTS `fbr_invoice_number`;
ALTER TABLE `tblinvoices` DROP COLUMN IF EXISTS `fbr_status`;
ALTER TABLE `tblinvoices` DROP COLUMN IF EXISTS `fbr_qr_code`;

-- Remove module options
DELETE FROM `tbloptions` WHERE `name` IN ('fbr_pos_enabled', 'fbr_pos_auto_send', 'fbr_pos_tax_rate', 'fbr_pos_server_url');

-- After running this script, you can activate the module safely