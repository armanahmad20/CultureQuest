<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fbr_pos_integration extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('fbr_pos_integration_model');
        $this->load->library('fbr_pos_integration/Fbr_api');
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
        
        if ($config_id) {
            $result = $this->fbr_pos_integration_model->update_store_config($config_id, $data);
            $message = 'Store configuration updated successfully';
        } else {
            $result = $this->fbr_pos_integration_model->create_store_config($data);
            $message = 'Store configuration created successfully';
        }

        if ($result) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save store configuration']);
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