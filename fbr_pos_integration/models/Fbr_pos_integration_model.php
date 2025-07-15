<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fbr_pos_integration_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats()
    {
        $stats = [];
        
        // Total invoices
        $stats['total_invoices'] = $this->db->count_all_results(db_prefix() . 'invoices');
        
        // FBR status counts
        $stats['confirmed_invoices'] = $this->db->where('fbr_status', 'confirmed')->count_all_results(db_prefix() . 'invoices');
        $stats['pending_invoices'] = $this->db->where('fbr_status', 'pending')->count_all_results(db_prefix() . 'invoices');
        $stats['failed_invoices'] = $this->db->where('fbr_status', 'failed')->count_all_results(db_prefix() . 'invoices');
        
        // Revenue calculation
        $this->db->select('SUM(total) as total_revenue');
        $this->db->where('fbr_status', 'confirmed');
        $revenue_result = $this->db->get(db_prefix() . 'invoices')->row();
        $stats['total_revenue'] = $revenue_result ? $revenue_result->total_revenue : 0;
        
        // Recent activities
        $stats['recent_activities'] = $this->db->limit(5)->order_by('created_at', 'DESC')->get('tblfbr_invoice_logs')->result();
        
        return $stats;
    }

    /**
     * Get store configurations
     */
    public function get_store_configs()
    {
        return $this->db->order_by('is_active', 'DESC')->order_by('created_at', 'DESC')->get('tblfbr_store_configs')->result();
    }

    /**
     * Get active store configuration
     */
    public function get_active_store_config()
    {
        return $this->db->where('is_active', 1)->get('tblfbr_store_configs')->row();
    }

    /**
     * Get store configuration by ID
     */
    public function get_store_config($id)
    {
        return $this->db->where('id', $id)->get('tblfbr_store_configs')->row();
    }

    /**
     * Create store configuration
     */
    public function create_store_config($data)
    {
        // If setting as active, deactivate others
        if ($data['is_active'] == 1) {
            $this->db->update('tblfbr_store_configs', ['is_active' => 0]);
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        // Debug: Check if table exists
        if (!$this->db->table_exists('tblfbr_store_configs')) {
            error_log('FBR DEBUG: Table tblfbr_store_configs does not exist!');
            log_message('error', 'FBR: Table tblfbr_store_configs does not exist!');
            return false;
        }
        
        // Debug: Log the SQL being executed
        error_log('FBR DEBUG: Creating store config with data: ' . json_encode($data));
        log_message('debug', 'FBR: Creating store config with data: ' . json_encode($data));
        
        $result = $this->db->insert('tblfbr_store_configs', $data);
        
        // Debug: Log the result and any errors
        if ($result) {
            $insert_id = $this->db->insert_id();
            error_log('FBR DEBUG: Insert successful, ID: ' . $insert_id);
            log_message('debug', 'FBR: Insert successful, ID: ' . $insert_id);
            
            // Verify the data was actually saved
            $saved_config = $this->db->where('id', $insert_id)->get('tblfbr_store_configs')->row();
            if ($saved_config) {
                error_log('FBR DEBUG: Data verified in DB: ' . json_encode($saved_config));
                log_message('debug', 'FBR: Data verified in DB: ' . json_encode($saved_config));
            } else {
                error_log('FBR DEBUG: Data not found in DB after insert!');
                log_message('error', 'FBR: Data not found in DB after insert!');
            }
        } else {
            $error = $this->db->error();
            error_log('FBR DEBUG: Insert failed: ' . $error['message']);
            log_message('error', 'FBR: Insert failed: ' . $error['message']);
        }
        
        return $result;
    }

    /**
     * Update store configuration
     */
    public function update_store_config($id, $data)
    {
        // If setting as active, deactivate others
        if (isset($data['is_active']) && $data['is_active'] == 1) {
            $this->db->update('tblfbr_store_configs', ['is_active' => 0]);
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update('tblfbr_store_configs', $data);
    }

    /**
     * Delete store configuration
     */
    public function delete_store_config($id)
    {
        return $this->db->where('id', $id)->delete('tblfbr_store_configs');
    }

    /**
     * Get PCT codes
     */
    public function get_pct_codes()
    {
        return $this->db->order_by('pct_code', 'ASC')->get('tblfbr_pct_codes')->result();
    }

    /**
     * Get active PCT codes
     */
    public function get_active_pct_codes()
    {
        return $this->db->where('is_active', 1)->order_by('pct_code', 'ASC')->get('tblfbr_pct_codes')->result();
    }

    /**
     * Create PCT code
     */
    public function create_pct_code($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('tblfbr_pct_codes', $data);
    }

    /**
     * Update PCT code
     */
    public function update_pct_code($id, $data)
    {
        return $this->db->where('id', $id)->update('tblfbr_pct_codes', $data);
    }

    /**
     * Delete PCT code
     */
    public function delete_pct_code($id)
    {
        return $this->db->where('id', $id)->delete('tblfbr_pct_codes');
    }

    /**
     * Get PCT code by code
     */
    public function get_pct_code_by_code($pct_code)
    {
        return $this->db->where('pct_code', $pct_code)->get('tblfbr_pct_codes')->row();
    }

    /**
     * Get FBR logs
     */
    public function get_logs($limit = 100)
    {
        return $this->db->limit($limit)->order_by('created_at', 'DESC')->get('tblfbr_invoice_logs')->result();
    }

    /**
     * Get recent logs
     */
    public function get_recent_logs($limit = 10)
    {
        return $this->db->limit($limit)->order_by('created_at', 'DESC')->get('tblfbr_invoice_logs')->result();
    }

    /**
     * Get logs by invoice ID
     */
    public function get_logs_by_invoice($invoice_id)
    {
        return $this->db->where('invoice_id', $invoice_id)->order_by('created_at', 'DESC')->get('tblfbr_invoice_logs')->result();
    }

    /**
     * Create FBR log
     */
    public function create_log($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('tblfbr_invoice_logs', $data);
    }

    /**
     * Update invoice FBR status
     */
    public function update_invoice_fbr_status($invoice_id, $status, $fbr_invoice_number = null, $qr_code = null)
    {
        $data = ['fbr_status' => $status];
        
        if ($fbr_invoice_number) {
            $data['fbr_invoice_number'] = $fbr_invoice_number;
        }
        
        if ($qr_code) {
            $data['fbr_qr_code'] = $qr_code;
        }
        
        return $this->db->where('id', $invoice_id)->update(db_prefix() . 'invoices', $data);
    }

    /**
     * Get invoice with FBR data
     */
    public function get_invoice_with_fbr_data($invoice_id)
    {
        return $this->db->where('id', $invoice_id)->get(db_prefix() . 'invoices')->row();
    }

    /**
     * Get invoices by FBR status
     */
    public function get_invoices_by_fbr_status($status, $limit = 50)
    {
        return $this->db->where('fbr_status', $status)->limit($limit)->order_by('date', 'DESC')->get(db_prefix() . 'invoices')->result();
    }

    /**
     * Get failed invoices for retry
     */
    public function get_failed_invoices_for_retry()
    {
        $this->db->where('fbr_status', 'failed');
        $this->db->where('date >', date('Y-m-d H:i:s', strtotime('-24 hours')));
        return $this->db->order_by('date', 'DESC')->get(db_prefix() . 'invoices')->result();
    }

    /**
     * Get invoice items with PCT codes
     */
    public function get_invoice_items_with_pct($invoice_id)
    {
        $this->db->select('ii.*, i.description as item_description, i.pct_code, pct.tax_rate');
        $this->db->from(db_prefix() . 'invoiceitems ii');
        $this->db->join(db_prefix() . 'items i', 'i.id = ii.rel_id', 'left');
        $this->db->join('tblfbr_pct_codes pct', 'pct.pct_code = i.pct_code', 'left');
        $this->db->where('ii.invoiceid', $invoice_id);
        return $this->db->get()->result();
    }

    /**
     * Bulk update invoice FBR status
     */
    public function bulk_update_fbr_status($invoice_ids, $status)
    {
        $this->db->where_in('id', $invoice_ids);
        return $this->db->update(db_prefix() . 'invoices', ['fbr_status' => $status]);
    }

    /**
     * Get FBR statistics by date range
     */
    public function get_fbr_stats_by_date_range($start_date, $end_date)
    {
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->select('fbr_status, COUNT(*) as count, SUM(total) as total_amount');
        $this->db->group_by('fbr_status');
        return $this->db->get(db_prefix() . 'invoices')->result();
    }

    /**
     * Get tax summary by PCT codes
     */
    public function get_tax_summary_by_pct_codes($start_date = null, $end_date = null)
    {
        $this->db->select('pct.pct_code, pct.description, pct.tax_rate, COUNT(ii.id) as item_count, SUM(ii.total) as total_amount');
        $this->db->from(db_prefix() . 'invoiceitems ii');
        $this->db->join(db_prefix() . 'items i', 'i.id = ii.rel_id', 'left');
        $this->db->join('tblfbr_pct_codes pct', 'pct.pct_code = i.pct_code', 'left');
        $this->db->join(db_prefix() . 'invoices inv', 'inv.id = ii.invoiceid', 'left');
        $this->db->where('inv.fbr_status', 'confirmed');
        
        if ($start_date && $end_date) {
            $this->db->where('inv.date >=', $start_date);
            $this->db->where('inv.date <=', $end_date);
        }
        
        $this->db->group_by('pct.pct_code');
        return $this->db->get()->result();
    }
}