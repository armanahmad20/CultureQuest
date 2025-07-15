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
    
    // Create FBR store configurations table
    if (!$CI->db->table_exists(db_prefix() . 'fbr_store_configs')) {
        $CI->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'store_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'store_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE
            ],
            'ntn' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => FALSE
            ],
            'strn' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => FALSE
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => FALSE
            ],
            'pos_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'pos_version' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => FALSE
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('store_id');
        $CI->dbforge->create_table(db_prefix() . 'fbr_store_configs');
    }
    
    // Create FBR invoice logs table
    if (!$CI->db->table_exists(db_prefix() . 'fbr_invoice_logs')) {
        $CI->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'store_config_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'fbr_invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE
            ],
            'request_data' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'response_data' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => FALSE
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ]
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('invoice_id');
        $CI->dbforge->create_table(db_prefix() . 'fbr_invoice_logs');
    }
    
    // Create PCT codes table
    if (!$CI->db->table_exists(db_prefix() . 'fbr_pct_codes')) {
        $CI->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'pct_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => FALSE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => FALSE
            ],
            'tax_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ]
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('pct_code');
        $CI->dbforge->create_table(db_prefix() . 'fbr_pct_codes');
    }
    
    // Add FBR fields to items table
    if (!$CI->db->field_exists('pct_code', db_prefix() . 'items')) {
        $CI->dbforge->add_column(db_prefix() . 'items', [
            'pct_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ]
        ]);
    }
    
    if (!$CI->db->field_exists('fbr_invoice_number', db_prefix() . 'invoices')) {
        $CI->dbforge->add_column(db_prefix() . 'invoices', [
            'fbr_invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'fbr_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pending'
            ],
            'fbr_qr_code' => [
                'type' => 'TEXT',
                'null' => TRUE
            ]
        ]);
    }
    
    // Insert default PCT codes
    $default_pct_codes = [
        ['pct_code' => '01111000', 'description' => 'Food and Beverages', 'tax_rate' => 17.00],
        ['pct_code' => '02111000', 'description' => 'Clothing and Textiles', 'tax_rate' => 17.00],
        ['pct_code' => '03111000', 'description' => 'Electronics and Appliances', 'tax_rate' => 17.00],
        ['pct_code' => '04111000', 'description' => 'Pharmaceuticals and Health', 'tax_rate' => 0.00],
        ['pct_code' => '05111000', 'description' => 'Automotive and Parts', 'tax_rate' => 17.00],
        ['pct_code' => '06111000', 'description' => 'Construction Materials', 'tax_rate' => 17.00],
        ['pct_code' => '07111000', 'description' => 'Books and Stationery', 'tax_rate' => 0.00],
        ['pct_code' => '08111000', 'description' => 'Cosmetics and Personal Care', 'tax_rate' => 17.00],
        ['pct_code' => '09111000', 'description' => 'Sports and Recreation', 'tax_rate' => 17.00],
        ['pct_code' => '10111000', 'description' => 'General Services', 'tax_rate' => 16.00]
    ];
    
    foreach ($default_pct_codes as $pct_code) {
        $pct_code['created_at'] = date('Y-m-d H:i:s');
        $CI->db->insert(db_prefix() . 'fbr_pct_codes', $pct_code);
    }
    
    // Create default store configuration
    $CI->db->insert(db_prefix() . 'fbr_store_configs', [
        'store_name' => 'Default Store',
        'store_id' => 'STORE001',
        'ntn' => '0000000000000',
        'strn' => 'STRN000000',
        'address' => 'Default Address, Pakistan',
        'pos_type' => 'Perfex CRM',
        'pos_version' => '1.0.0',
        'ip_address' => '127.0.0.1',
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Add options
    add_option('fbr_pos_enabled', 1);
    add_option('fbr_pos_auto_send', 1);
    add_option('fbr_pos_tax_rate', 17.00);
    add_option('fbr_pos_server_url', 'https://fbr.gov.pk/api/pos');
}

/**
 * Module deactivation hook
 */
function fbr_pos_deactivation_hook()
{
    // Clean up options
    delete_option('fbr_pos_enabled');
    delete_option('fbr_pos_auto_send');
    delete_option('fbr_pos_tax_rate');
    delete_option('fbr_pos_server_url');
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