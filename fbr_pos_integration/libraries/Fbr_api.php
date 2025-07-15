<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fbr_api
{
    private $CI;
    private $server_url;
    private $store_config;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('fbr_pos_integration_model');
        $this->server_url = get_option('fbr_pos_server_url');
        $this->store_config = $this->CI->fbr_pos_integration_model->get_active_store_config();
    }

    /**
     * Send invoice to FBR
     */
    public function send_invoice($invoice_id)
    {
        if (!$this->store_config) {
            throw new Exception('No active store configuration found');
        }

        // Get invoice data
        $invoice = $this->CI->fbr_pos_integration_model->get_invoice_with_fbr_data($invoice_id);
        if (!$invoice) {
            throw new Exception('Invoice not found');
        }

        // Get invoice items with PCT codes
        $items = $this->CI->fbr_pos_integration_model->get_invoice_items_with_pct($invoice_id);

        // Prepare FBR invoice data
        $fbr_data = $this->prepare_fbr_invoice_data($invoice, $items);

        // Log the request
        $log_data = [
            'invoice_id' => $invoice_id,
            'store_config_id' => $this->store_config->id,
            'action' => 'send_invoice',
            'request_data' => json_encode($fbr_data),
            'status' => 'pending'
        ];

        try {
            // Send to FBR API
            $response = $this->make_api_call('/submit-invoice', $fbr_data);
            
            if ($response['success']) {
                // Update invoice status
                $this->CI->fbr_pos_integration_model->update_invoice_fbr_status(
                    $invoice_id, 
                    'confirmed', 
                    $response['fbr_invoice_number'],
                    $this->generate_qr_code($response['fbr_invoice_number'], $invoice->total)
                );
                
                $log_data['status'] = 'success';
                $log_data['response_data'] = json_encode($response);
                $log_data['fbr_invoice_number'] = $response['fbr_invoice_number'];
            } else {
                $this->CI->fbr_pos_integration_model->update_invoice_fbr_status($invoice_id, 'failed');
                $log_data['status'] = 'error';
                $log_data['response_data'] = json_encode($response);
                $log_data['error_message'] = $response['message'] ?? 'Unknown error';
            }
            
            $this->CI->fbr_pos_integration_model->create_log($log_data);
            return $response;
            
        } catch (Exception $e) {
            $this->CI->fbr_pos_integration_model->update_invoice_fbr_status($invoice_id, 'failed');
            $log_data['status'] = 'error';
            $log_data['error_message'] = $e->getMessage();
            $this->CI->fbr_pos_integration_model->create_log($log_data);
            throw $e;
        }
    }

    /**
     * Check FBR server status
     */
    public function check_server_status()
    {
        try {
            $response = $this->make_api_call('/status', [], 'GET');
            return [
                'success' => true,
                'online' => $response['online'] ?? false,
                'message' => $response['message'] ?? 'Server status checked'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'online' => false,
                'message' => 'Failed to connect to FBR servers: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retry failed invoice
     */
    public function retry_invoice($invoice_id)
    {
        return $this->send_invoice($invoice_id);
    }

    /**
     * Verify invoice with FBR
     */
    public function verify_invoice($fbr_invoice_number)
    {
        try {
            $response = $this->make_api_call('/verify-invoice', [
                'fbr_invoice_number' => $fbr_invoice_number
            ]);
            
            return $response;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to verify invoice: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update invoice status
     */
    public function update_invoice_status($invoice_id)
    {
        $invoice = $this->CI->fbr_pos_integration_model->get_invoice_with_fbr_data($invoice_id);
        
        if (!$invoice || !$invoice->fbr_invoice_number) {
            return;
        }

        try {
            $response = $this->make_api_call('/invoice-status', [
                'fbr_invoice_number' => $invoice->fbr_invoice_number
            ]);
            
            if ($response['success'] && isset($response['status'])) {
                $this->CI->fbr_pos_integration_model->update_invoice_fbr_status(
                    $invoice_id, 
                    $response['status']
                );
            }
            
        } catch (Exception $e) {
            // Log error but don't throw exception for status checks
            $this->CI->fbr_pos_integration_model->create_log([
                'invoice_id' => $invoice_id,
                'store_config_id' => $this->store_config->id,
                'action' => 'status_check',
                'status' => 'error',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Prepare FBR invoice data
     */
    private function prepare_fbr_invoice_data($invoice, $items)
    {
        $fbr_items = [];
        
        foreach ($items as $item) {
            $fbr_items[] = [
                'description' => $item->item_description ?? $item->description,
                'quantity' => (int)$item->qty,
                'rate' => (float)$item->rate,
                'total' => (float)$item->total,
                'pct_code' => $item->pct_code,
                'tax_rate' => (float)($item->tax_rate ?? 17.00)
            ];
        }

        return [
            'invoice_id' => $invoice->id,
            'store_id' => $this->store_config->store_id,
            'invoice_number' => $invoice->number,
            'invoice_date' => $invoice->date,
            'customer_name' => $invoice->client_name ?? 'Walk-in Customer',
            'customer_ntn' => $invoice->client_ntn ?? '',
            'items' => $fbr_items,
            'subtotal' => (float)$invoice->subtotal,
            'tax_amount' => (float)$invoice->total_tax,
            'discount_amount' => (float)$invoice->discount_total,
            'total_amount' => (float)$invoice->total,
            'payment_mode' => $this->determine_payment_mode($invoice),
            'store_config' => [
                'store_name' => $this->store_config->store_name,
                'ntn' => $this->store_config->ntn,
                'strn' => $this->store_config->strn,
                'address' => $this->store_config->address,
                'pos_type' => $this->store_config->pos_type,
                'pos_version' => $this->store_config->pos_version
            ]
        ];
    }

    /**
     * Make API call to FBR SDC (Sales Data Controller)
     * SDC runs on Windows machine at business premises
     */
    private function make_api_call($endpoint, $data = [], $method = 'POST')
    {
        // FBR SDC typically runs on localhost:8080 or local network IP
        $sdc_url = $this->store_config->sdc_url ?? 'http://localhost:8080';
        $url = rtrim($sdc_url, '/') . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Perfex-FBR-POS/1.0'
        ]);
        
        // Disable SSL verification for local SDC connections
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        curl_close($ch);
        
        if ($curl_error) {
            throw new Exception('Cannot connect to FBR SDC: ' . $curl_error . '. Ensure FBR SDC is running on the Windows machine.');
        }
        
        if ($http_code !== 200) {
            throw new Exception('FBR SDC returned error: HTTP ' . $http_code);
        }
        
        $decoded_response = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid response from FBR SDC');
        }
        
        return $decoded_response;
    }

    /**
     * Determine payment mode from invoice
     */
    private function determine_payment_mode($invoice)
    {
        // Check payment records
        $this->CI->load->model('payments_model');
        $payments = $this->CI->payments_model->get_invoice_payments($invoice->id);
        
        if (!empty($payments)) {
            $payment = $payments[0];
            if (stripos($payment->paymentmode, 'cash') !== false) {
                return 'cash';
            } elseif (stripos($payment->paymentmode, 'card') !== false || stripos($payment->paymentmode, 'credit') !== false) {
                return 'card';
            } elseif (stripos($payment->paymentmode, 'cheque') !== false || stripos($payment->paymentmode, 'check') !== false) {
                return 'cheque';
            }
        }
        
        return 'cash'; // Default to cash
    }

    /**
     * Generate QR code data
     */
    private function generate_qr_code($fbr_invoice_number, $total_amount)
    {
        return "FBR:{$fbr_invoice_number}:{$total_amount}:" . date('Y-m-d\TH:i:s');
    }

    /**
     * Get FBR invoice format
     */
    public function get_fbr_invoice_format($invoice_id)
    {
        $invoice = $this->CI->fbr_pos_integration_model->get_invoice_with_fbr_data($invoice_id);
        $items = $this->CI->fbr_pos_integration_model->get_invoice_items_with_pct($invoice_id);
        
        if (!$invoice) {
            return null;
        }
        
        return [
            'invoice_number' => $invoice->number,
            'fbr_invoice_number' => $invoice->fbr_invoice_number,
            'date' => $invoice->date,
            'store_config' => $this->store_config,
            'items' => $items,
            'totals' => [
                'subtotal' => $invoice->subtotal,
                'tax_amount' => $invoice->total_tax,
                'discount_amount' => $invoice->discount_total,
                'total_amount' => $invoice->total
            ],
            'qr_code' => $invoice->fbr_qr_code,
            'status' => $invoice->fbr_status
        ];
    }

    /**
     * Bulk send invoices to FBR
     */
    public function bulk_send_invoices($invoice_ids)
    {
        $results = [];
        
        foreach ($invoice_ids as $invoice_id) {
            try {
                $result = $this->send_invoice($invoice_id);
                $results[$invoice_id] = $result;
            } catch (Exception $e) {
                $results[$invoice_id] = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Get FBR compliance report
     */
    public function get_compliance_report($start_date, $end_date)
    {
        $stats = $this->CI->fbr_pos_integration_model->get_fbr_stats_by_date_range($start_date, $end_date);
        $pct_summary = $this->CI->fbr_pos_integration_model->get_tax_summary_by_pct_codes($start_date, $end_date);
        
        return [
            'period' => [
                'start_date' => $start_date,
                'end_date' => $end_date
            ],
            'statistics' => $stats,
            'pct_summary' => $pct_summary,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}