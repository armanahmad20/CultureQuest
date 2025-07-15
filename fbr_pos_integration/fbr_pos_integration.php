<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: FBR POS Integration
Description: Integrates with Pakistan's Federal Board of Revenue (FBR) POS system for real-time sales reporting and tax compliance. Includes PCT codes support for product classification.
Version: 1.0.0
Author: FBR POS Integration Team
Requires at least: 2.3.0
*/

define('FBR_POS_MODULE_NAME', 'fbr_pos_integration');
define('FBR_POS_MODULE_VERSION', '1.0.0');

// Module activation hook
register_activation_hook(FBR_POS_MODULE_NAME, 'fbr_pos_activation_hook');

// Module deactivation hook
register_deactivation_hook(FBR_POS_MODULE_NAME, 'fbr_pos_deactivation_hook');

// Menu items
hooks()->add_action('admin_init', 'fbr_pos_init_menu_items');

// Assets
hooks()->add_action('app_admin_head', 'fbr_pos_add_head_components');
hooks()->add_action('app_admin_footer', 'fbr_pos_add_footer_components');

// Invoice hooks
hooks()->add_action('after_invoice_added', 'fbr_pos_send_invoice_to_fbr');
hooks()->add_action('after_invoice_updated', 'fbr_pos_update_invoice_status');

/**
 * Module activation hook
 */
function fbr_pos_activation_hook()
{
    $CI = &get_instance();
    $CI->load->dbforge();
    
    // Create tables only if they don't exist - DO NOT DROP EXISTING TABLES
    create_fbr_pct_codes_table();
    create_fbr_store_configs_table();
    create_fbr_invoice_logs_table();
}

function create_fbr_pct_codes_table()
{
    $CI = &get_instance();
    
    // Check if table already exists
    if ($CI->db->table_exists('tblfbr_pct_codes')) {
        return; // Table exists, don't recreate
    }
    
    // Create table using direct SQL to avoid prefix issues
    $sql = "CREATE TABLE IF NOT EXISTS `tblfbr_pct_codes` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `pct_code` varchar(20) NOT NULL,
        `description` text NOT NULL,
        `tax_rate` decimal(5,2) DEFAULT 17.00,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_pct_code` (`pct_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $CI->db->query($sql);
    
    // Insert sample data
    $sample_data = [
        ['01111000', 'Food and Beverages', 17.00],
        ['02111000', 'Clothing and Textiles', 17.00],
        ['03111000', 'Electronics and Appliances', 17.00],
        ['04111000', 'Pharmaceuticals and Health', 0.00],
        ['05111000', 'Automotive and Parts', 17.00]
    ];
    
    foreach ($sample_data as $data) {
        $CI->db->query("INSERT IGNORE INTO `tblfbr_pct_codes` (`pct_code`, `description`, `tax_rate`, `is_active`, `created_at`) VALUES (?, ?, ?, 1, NOW())", $data);
    }
}

function create_fbr_store_configs_table()
{
    $CI = &get_instance();
    
    // Check if table already exists
    if ($CI->db->table_exists('tblfbr_store_configs')) {
        return; // Table exists, don't recreate
    }
    
    // Create table using direct SQL
    $sql = "CREATE TABLE IF NOT EXISTS `tblfbr_store_configs` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $CI->db->query($sql);
    
    // Insert default store config
    $CI->db->query("INSERT IGNORE INTO `tblfbr_store_configs` (`store_name`, `store_id`, `ntn`, `strn`, `address`, `pos_type`, `pos_version`, `ip_address`, `sdc_url`, `is_active`, `created_at`) VALUES ('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 'http://localhost:8080', 1, NOW())");
}

function create_fbr_invoice_logs_table()
{
    $CI = &get_instance();
    
    // Check if table already exists
    if ($CI->db->table_exists('tblfbr_invoice_logs')) {
        return; // Table exists, don't recreate
    }
    
    // Create table using direct SQL
    $sql = "CREATE TABLE IF NOT EXISTS `tblfbr_invoice_logs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `invoice_id` int(11) NOT NULL,
        `store_config_id` int(11) NOT NULL,
        `fbr_invoice_number` varchar(100) DEFAULT NULL,
        `action` varchar(50) NOT NULL,
        `request_data` text DEFAULT NULL,
        `response_data` text DEFAULT NULL,
        `status` varchar(20) NOT NULL,
        `error_message` text DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_invoice_id` (`invoice_id`),
        INDEX `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $CI->db->query($sql);
}

/**
 * Module deactivation hook
 */
function fbr_pos_deactivation_hook()
{
    // Keep tables intact during deactivation
    // Only clean up if necessary
}

/**
 * Initialize menu items
 */
function fbr_pos_init_menu_items()
{
    $CI = &get_instance();
    
    if (has_permission('fbr_pos', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('fbr_pos', [
            'name' => 'FBR POS Integration',
            'href' => admin_url('fbr_pos_integration'),
            'icon' => 'fa fa-receipt',
            'position' => 30,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('fbr_pos', [
            'slug' => 'fbr_pos_dashboard',
            'name' => 'Dashboard',
            'href' => admin_url('fbr_pos_integration'),
            'position' => 1,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('fbr_pos', [
            'slug' => 'fbr_pos_store_config',
            'name' => 'Store Configuration',
            'href' => admin_url('fbr_pos_integration/store_config'),
            'position' => 2,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('fbr_pos', [
            'slug' => 'fbr_pos_pct_codes',
            'name' => 'PCT Codes',
            'href' => admin_url('fbr_pos_integration/pct_codes'),
            'position' => 3,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('fbr_pos', [
            'slug' => 'fbr_pos_logs',
            'name' => 'FBR Logs',
            'href' => admin_url('fbr_pos_integration/logs'),
            'position' => 4,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('fbr_pos', [
            'slug' => 'fbr_pos_settings',
            'name' => 'Settings',
            'href' => admin_url('fbr_pos_integration/settings'),
            'position' => 5,
        ]);
    }
}

/**
 * Add head components
 */
function fbr_pos_add_head_components()
{
    $CI = &get_instance();
    
    if (strpos($CI->router->fetch_class(), 'fbr_pos') !== false) {
        echo '<link href="' . module_dir_url(FBR_POS_MODULE_NAME, 'assets/css/fbr_pos.css') . '" rel="stylesheet" type="text/css">';
    }
}

/**
 * Add footer components
 */
function fbr_pos_add_footer_components()
{
    $CI = &get_instance();
    
    if (strpos($CI->router->fetch_class(), 'fbr_pos') !== false) {
        echo '<script src="' . module_dir_url(FBR_POS_MODULE_NAME, 'assets/js/fbr_pos.js') . '"></script>';
    }
}

/**
 * Send invoice to FBR after creation
 */
function fbr_pos_send_invoice_to_fbr($invoice_id)
{
    if (!get_option('fbr_pos_enabled') || !get_option('fbr_pos_auto_send')) {
        return;
    }
    
    $CI = &get_instance();
    $CI->load->library('fbr_pos_integration/Fbr_api');
    
    try {
        $CI->fbr_api->send_invoice($invoice_id);
    } catch (Exception $e) {
        log_activity('FBR POS Error: ' . $e->getMessage());
    }
}

/**
 * Update invoice status
 */
function fbr_pos_update_invoice_status($invoice_id)
{
    if (!get_option('fbr_pos_enabled')) {
        return;
    }
    
    $CI = &get_instance();
    $CI->load->library('fbr_pos_integration/Fbr_api');
    
    try {
        $CI->fbr_api->update_invoice_status($invoice_id);
    } catch (Exception $e) {
        log_activity('FBR POS Error: ' . $e->getMessage());
    }
}

/**
 * Get module version
 */
function fbr_pos_get_version()
{
    return FBR_POS_MODULE_VERSION;
}

/**
 * Check if module is activated
 */
function fbr_pos_is_activated()
{
    return (bool) get_option('fbr_pos_enabled');
}