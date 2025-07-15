<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fbr_pos_integration extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('fbr_pos_integration_model');
        $this->load->library('fbr_pos_integration/Fbr_api');
        $this->load->library('form_validation');
    }

    /**
     * Dashboard view
     */
    public function index()
    {
        if (!has_permission('fbr_pos', '', 'view')) {
            access_denied('fbr_pos');
        }

        $data['title'] = 'FBR POS Integration Dashboard';
        $data['stats'] = $this->fbr_pos_integration_model->get_dashboard_stats();
        $data['recent_logs'] = $this->fbr_pos_integration_model->get_recent_logs(10);
        $data['active_store'] = $this->fbr_pos_integration_model->get_active_store_config();
        
        $this->load->view('fbr_pos_integration/dashboard', $data);
    }

    /**
     * Store configuration management
     */
    public function store_config()
    {
        if (!has_permission('fbr_pos', '', 'view')) {
            access_denied('fbr_pos');
        }

        if ($this->input->post()) {
            $this->handle_store_config_submit();
        }

        $data['title'] = 'Store Configuration';
        $data['store_configs'] = $this->fbr_pos_integration_model->get_store_configs();
        
        $this->load->view('fbr_pos_integration/store_config', $data);
    }

    /**
     * PCT Codes management
     */
    public function pct_codes()
    {
        if (!has_permission('fbr_pos', '', 'view')) {
            access_denied('fbr_pos');
        }

        if ($this->input->post()) {
            $this->handle_pct_code_submit();
        }

        $data['title'] = 'PCT Codes Management';
        $data['pct_codes'] = $this->fbr_pos_integration_model->get_pct_codes();
        
        $this->load->view('fbr_pos_integration/pct_codes', $data);
    }

    /**
     * FBR Logs view
     */
    public function logs()
    {
        if (!has_permission('fbr_pos', '', 'view')) {
            access_denied('fbr_pos');
        }

        $data['title'] = 'FBR Communication Logs';
        $data['logs'] = $this->fbr_pos_integration_model->get_logs();
        
        $this->load->view('fbr_pos_integration/logs', $data);
    }

    /**
     * Settings page
     */
    public function settings()
    {
        if (!has_permission('fbr_pos', '', 'view')) {
            access_denied('fbr_pos');
        }

        if ($this->input->post()) {
            $this->handle_settings_submit();
        }

        $data['title'] = 'FBR POS Settings';
        $data['settings'] = [
            'fbr_pos_enabled' => get_option('fbr_pos_enabled'),
            'fbr_pos_auto_send' => get_option('fbr_pos_auto_send'),
            'fbr_pos_tax_rate' => get_option('fbr_pos_tax_rate'),
            'fbr_pos_server_url' => get_option('fbr_pos_server_url')
        ];
        
        $this->load->view('fbr_pos_integration/settings', $data);
    }

    /**
     * AJAX: Send invoice to FBR
     */
    public function send_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $invoice_id = $this->input->post('invoice_id');
        
        if (!$invoice_id) {
            echo json_encode(['success' => false, 'message' => 'Invoice ID is required']);
            return;
        }

        try {
            $result = $this->fbr_api->send_invoice($invoice_id);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Check FBR server status
     */
    public function check_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        try {
            $result = $this->fbr_api->check_server_status();
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Retry failed invoice
     */
    public function retry_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $invoice_id = $this->input->post('invoice_id');
        
        if (!$invoice_id) {
            echo json_encode(['success' => false, 'message' => 'Invoice ID is required']);
            return;
        }

        try {
            $result = $this->fbr_api->retry_invoice($invoice_id);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Save store config
     */
    public function save_store_config()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('store_name', 'Store Name', 'required');
        $this->form_validation->set_rules('store_id', 'Store ID', 'required');
        $this->form_validation->set_rules('ntn', 'NTN', 'required|exact_length[13]');
        $this->form_validation->set_rules('strn', 'STRN', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false, 
                'message' => validation_errors()
            ]);
            return;
        }

        $data = [
            'store_name' => $this->input->post('store_name'),
            'store_id' => $this->input->post('store_id'),
            'ntn' => $this->input->post('ntn'),
            'strn' => $this->input->post('strn'),
            'address' => $this->input->post('address'),
            'pos_type' => $this->input->post('pos_type'),
            'pos_version' => $this->input->post('pos_version'),
            'ip_address' => $this->input->post('ip_address'),
            'sdc_url' => $this->input->post('sdc_url'),
            'sdc_username' => $this->input->post('sdc_username'),
            'sdc_password' => $this->input->post('sdc_password'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        ];

        $config_id = $this->input->post('config_id');
        
        try {
            // Debug: Log the data being saved (also write to error log for visibility)
            error_log('FBR DEBUG: Attempting to save config data: ' . json_encode($data));
            log_message('debug', 'FBR: Attempting to save config data: ' . json_encode($data));
            
            if ($config_id) {
                $result = $this->fbr_pos_integration_model->update_store_config($config_id, $data);
                $message = 'Store configuration updated successfully';
                error_log('FBR DEBUG: Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
                log_message('debug', 'FBR: Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            } else {
                $result = $this->fbr_pos_integration_model->create_store_config($data);
                $message = 'Store configuration created successfully';
                error_log('FBR DEBUG: Create result: ' . ($result ? 'SUCCESS' : 'FAILED'));
                log_message('debug', 'FBR: Create result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            }

            // Debug: Check if data was actually saved
            $configs = $this->fbr_pos_integration_model->get_store_configs();
            error_log('FBR DEBUG: Total configs after save: ' . count($configs));
            log_message('debug', 'FBR: Total configs after save: ' . count($configs));

            if ($result) {
                // Get fresh count after save
                $final_count = $this->fbr_pos_integration_model->get_store_configs();
                $debug_info = [
                    'operation' => $config_id ? 'update' : 'create',
                    'result' => 'SUCCESS',
                    'configs_before' => count($configs),
                    'configs_after' => count($final_count),
                    'data_saved' => $data
                ];
                echo json_encode(['success' => true, 'message' => $message, 'debug' => $debug_info]);
            } else {
                $db_error = $this->db->error();
                $error_msg = 'Database error: ' . $db_error['message'];
                error_log('FBR DEBUG: Database error: ' . $error_msg);
                log_message('error', 'FBR: Database error: ' . $error_msg);
                
                $debug_info = [
                    'operation' => $config_id ? 'update' : 'create',
                    'result' => 'FAILED',
                    'error' => $error_msg,
                    'data_attempted' => $data
                ];
                echo json_encode(['success' => false, 'message' => $error_msg, 'debug' => $debug_info]);
            }
        } catch (Exception $e) {
            error_log('FBR DEBUG: Exception in save_store_config: ' . $e->getMessage());
            log_message('error', 'FBR: Exception in save_store_config: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Test database connection
     */
    public function test_db_connection()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        try {
            // Test if table exists
            $table_exists = $this->db->table_exists('tblfbr_store_configs');
            
            // Count existing records
            $count = $this->db->count_all('tblfbr_store_configs');
            
            // Get all configs
            $configs = $this->fbr_pos_integration_model->get_store_configs();
            
            // Check environment and logging settings
            $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'unknown';
            $log_threshold = $this->config->item('log_threshold');
            
            echo json_encode([
                'success' => true,
                'table_exists' => $table_exists,
                'record_count' => $count,
                'configs_found' => count($configs),
                'configs' => $configs,
                'environment' => $environment,
                'log_threshold' => $log_threshold,
                'debug_enabled' => ($log_threshold >= 4) ? 'YES' : 'NO'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Fix database schema
     */
    public function fix_database_schema()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        try {
            // Run database schema fix
            $fix_result = $this->run_database_schema_fix();
            
            // Test if table exists
            $table_exists = $this->db->table_exists('tblfbr_store_configs');
            
            // Count existing records
            $count = $this->db->count_all('tblfbr_store_configs');
            
            // Get all configs
            $configs = $this->fbr_pos_integration_model->get_store_configs();
            
            echo json_encode([
                'success' => true,
                'fix_result' => $fix_result,
                'table_exists' => $table_exists,
                'record_count' => $count,
                'configs_found' => count($configs),
                'configs' => $configs
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database fix failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Run database schema fix
     */
    private function run_database_schema_fix()
    {
        try {
            // Drop incorrectly named tables
            $this->db->query('DROP TABLE IF EXISTS `tbltblfbr_store_configs`');
            $this->db->query('DROP TABLE IF EXISTS `tbltblfbr_invoice_logs`');
            $this->db->query('DROP TABLE IF EXISTS `tbltblfbr_pct_codes`');
            
            // Check if correct table exists and update schema if needed
            if ($this->db->table_exists('tblfbr_store_configs')) {
                // Check and add missing columns one by one
                $fields = $this->db->list_fields('tblfbr_store_configs');
                
                if (!in_array('sdc_url', $fields)) {
                    $this->db->query("ALTER TABLE `tblfbr_store_configs` ADD COLUMN `sdc_url` varchar(255) DEFAULT 'http://localhost:8080' AFTER `ip_address`");
                }
                
                if (!in_array('sdc_username', $fields)) {
                    $this->db->query("ALTER TABLE `tblfbr_store_configs` ADD COLUMN `sdc_username` varchar(100) DEFAULT NULL AFTER `sdc_url`");
                }
                
                if (!in_array('sdc_password', $fields)) {
                    $this->db->query("ALTER TABLE `tblfbr_store_configs` ADD COLUMN `sdc_password` varchar(100) DEFAULT NULL AFTER `sdc_username`");
                }
                
                if (!in_array('updated_at', $fields)) {
                    $this->db->query("ALTER TABLE `tblfbr_store_configs` ADD COLUMN `updated_at` datetime DEFAULT NULL AFTER `created_at`");
                }
            } else {
                // Create new table with complete schema
                $this->db->query("CREATE TABLE `tblfbr_store_configs` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            }
            
            // Create other tables
            $this->db->query("CREATE TABLE IF NOT EXISTS `tblfbr_invoice_logs` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            $this->db->query("CREATE TABLE IF NOT EXISTS `tblfbr_pct_codes` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `pct_code` varchar(20) NOT NULL,
                `description` text NOT NULL,
                `tax_rate` decimal(5,2) DEFAULT 17.00,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_pct_code` (`pct_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Insert default data if table is empty
            $count = $this->db->count_all('tblfbr_store_configs');
            if ($count == 0) {
                $this->db->query("INSERT INTO `tblfbr_store_configs` (`store_name`, `store_id`, `ntn`, `strn`, `address`, `pos_type`, `pos_version`, `ip_address`, `sdc_url`, `is_active`, `created_at`) 
                    VALUES ('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 'http://localhost:8080', 1, NOW())");
            }
            
            // Insert sample PCT codes
            $pct_count = $this->db->count_all('tblfbr_pct_codes');
            if ($pct_count == 0) {
                $this->db->query("INSERT INTO `tblfbr_pct_codes` (`pct_code`, `description`, `tax_rate`, `is_active`, `created_at`) VALUES 
                    ('01111000', 'Food and Beverages', 17.00, 1, NOW()),
                    ('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),
                    ('03111000', 'Electronics and Appliances', 17.00, 1, NOW()),
                    ('04111000', 'Pharmaceuticals and Health', 0.00, 1, NOW()),
                    ('05111000', 'Automotive and Parts', 17.00, 1, NOW())");
            }
            
            return 'Database schema fix applied successfully';
            
        } catch (Exception $e) {
            return 'Database schema fix failed: ' . $e->getMessage();
        }
    }

    /**
     * AJAX: Get store config
     */
    public function get_store_config()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $config_id = $this->input->post('id');
        
        if (!$config_id) {
            echo json_encode(['success' => false, 'message' => 'Config ID is required']);
            return;
        }

        $config = $this->fbr_pos_integration_model->get_store_config($config_id);
        if ($config) {
            echo json_encode(['success' => true, 'config' => $config]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Configuration not found']);
        }
    }

    /**
     * AJAX: Delete store config
     */
    public function delete_store_config()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $config_id = $this->input->post('id');
        
        if (!$config_id) {
            echo json_encode(['success' => false, 'message' => 'Config ID is required']);
            return;
        }

        $result = $this->fbr_pos_integration_model->delete_store_config($config_id);
        echo json_encode(['success' => $result]);
    }

    /**
     * AJAX: Delete PCT code
     */
    public function delete_pct_code()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $pct_id = $this->input->post('id');
        
        if (!$pct_id) {
            echo json_encode(['success' => false, 'message' => 'PCT ID is required']);
            return;
        }

        $result = $this->fbr_pos_integration_model->delete_pct_code($pct_id);
        echo json_encode(['success' => $result]);
    }

    /**
     * Handle store configuration form submission
     */
    private function handle_store_config_submit()
    {
        $this->form_validation->set_rules('store_name', 'Store Name', 'required');
        $this->form_validation->set_rules('store_id', 'Store ID', 'required');
        $this->form_validation->set_rules('ntn', 'NTN', 'required|exact_length[13]');
        $this->form_validation->set_rules('strn', 'STRN', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'store_name' => $this->input->post('store_name'),
                'store_id' => $this->input->post('store_id'),
                'ntn' => $this->input->post('ntn'),
                'strn' => $this->input->post('strn'),
                'address' => $this->input->post('address'),
                'pos_type' => $this->input->post('pos_type'),
                'pos_version' => $this->input->post('pos_version'),
                'ip_address' => $this->input->post('ip_address'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            $config_id = $this->input->post('config_id');
            
            if ($config_id) {
                $result = $this->fbr_pos_integration_model->update_store_config($config_id, $data);
                $message = 'Store configuration updated successfully';
            } else {
                $result = $this->fbr_pos_integration_model->create_store_config($data);
                $message = 'Store configuration created successfully';
            }

            if ($result) {
                set_alert('success', $message);
            } else {
                set_alert('danger', 'Failed to save store configuration');
            }
        }
    }

    /**
     * Handle PCT code form submission
     */
    private function handle_pct_code_submit()
    {
        $this->form_validation->set_rules('pct_code', 'PCT Code', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('tax_rate', 'Tax Rate', 'required|numeric');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'pct_code' => $this->input->post('pct_code'),
                'description' => $this->input->post('description'),
                'tax_rate' => $this->input->post('tax_rate'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            $pct_id = $this->input->post('pct_id');
            
            if ($pct_id) {
                $result = $this->fbr_pos_integration_model->update_pct_code($pct_id, $data);
                $message = 'PCT code updated successfully';
            } else {
                $result = $this->fbr_pos_integration_model->create_pct_code($data);
                $message = 'PCT code created successfully';
            }

            if ($result) {
                set_alert('success', $message);
            } else {
                set_alert('danger', 'Failed to save PCT code');
            }
        }
    }

    /**
     * Handle settings form submission
     */
    private function handle_settings_submit()
    {
        $settings = [
            'fbr_pos_enabled' => $this->input->post('fbr_pos_enabled') ? 1 : 0,
            'fbr_pos_auto_send' => $this->input->post('fbr_pos_auto_send') ? 1 : 0,
            'fbr_pos_tax_rate' => $this->input->post('fbr_pos_tax_rate'),
            'fbr_pos_server_url' => $this->input->post('fbr_pos_server_url')
        ];

        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }

        set_alert('success', 'Settings updated successfully');
    }
}