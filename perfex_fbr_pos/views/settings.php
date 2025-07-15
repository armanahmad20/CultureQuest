<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="customer-profile-group-heading">
                                    <i class="fa fa-cogs"></i> FBR POS Integration Settings
                                </h4>
                                <hr>
                            </div>
                        </div>
                        
                        <form method="POST" id="fbr-settings-form">
                            <div class="fbr-settings-grid">
                                <div class="fbr-settings-card">
                                    <h5><i class="fa fa-toggle-on"></i> General Settings</h5>
                                    
                                    <div class="form-group">
                                        <label class="control-label">
                                            <input type="checkbox" name="fbr_pos_enabled" 
                                                   <?php echo $settings['fbr_pos_enabled'] ? 'checked' : ''; ?>>
                                            Enable FBR POS Integration
                                        </label>
                                        <small class="fbr-help-text">Enable or disable the entire FBR POS integration system</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label">
                                            <input type="checkbox" name="fbr_pos_auto_send" 
                                                   <?php echo $settings['fbr_pos_auto_send'] ? 'checked' : ''; ?>>
                                            Auto-send Invoices to FBR
                                        </label>
                                        <small class="fbr-help-text">Automatically send invoices to FBR when created</small>
                                    </div>
                                </div>
                                
                                <div class="fbr-settings-card">
                                    <h5><i class="fa fa-percent"></i> Tax Configuration</h5>
                                    
                                    <div class="form-group">
                                        <label for="fbr_pos_tax_rate">Default Tax Rate (%)</label>
                                        <input type="number" class="form-control" name="fbr_pos_tax_rate" 
                                               id="fbr_pos_tax_rate" min="0" max="100" step="0.01"
                                               value="<?php echo $settings['fbr_pos_tax_rate']; ?>">
                                        <small class="fbr-help-text">Default tax rate for items without PCT codes</small>
                                    </div>
                                </div>
                                
                                <div class="fbr-settings-card">
                                    <h5><i class="fa fa-server"></i> FBR Server Configuration</h5>
                                    
                                    <div class="form-group">
                                        <label for="fbr_pos_server_url">FBR Server URL</label>
                                        <input type="url" class="form-control" name="fbr_pos_server_url" 
                                               id="fbr_pos_server_url" 
                                               value="<?php echo $settings['fbr_pos_server_url']; ?>">
                                        <small class="fbr-help-text">FBR API server endpoint URL</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="button" class="btn btn-info btn-sm" onclick="testFbrConnection()">
                                            <i class="fa fa-plug"></i> Test Connection
                                        </button>
                                        <div id="connection-test-result" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Save Settings
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="resetSettings()">
                                        <i class="fa fa-refresh"></i> Reset to Defaults
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="fa fa-info-circle"></i> System Information
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Module Version:</strong> <?php echo fbr_pos_get_version(); ?></p>
                                                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                                                <p><strong>cURL Support:</strong> 
                                                    <?php if (function_exists('curl_version')): ?>
                                                        <span class="text-success">Enabled</span>
                                                    <?php else: ?>
                                                        <span class="text-danger">Disabled</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Database Tables:</strong> 
                                                    <?php 
                                                    $tables = ['fbr_store_configs', 'fbr_invoice_logs', 'fbr_pct_codes'];
                                                    $exists = 0;
                                                    foreach ($tables as $table) {
                                                        if ($this->db->table_exists(db_prefix() . $table)) {
                                                            $exists++;
                                                        }
                                                    }
                                                    echo $exists . '/' . count($tables) . ' tables exist';
                                                    ?>
                                                </p>
                                                <p><strong>Last Update:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="fa fa-exclamation-triangle"></i> Important Notes
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <ul>
                                            <li>Ensure your store configuration is set up before enabling FBR integration</li>
                                            <li>Test the FBR connection regularly to ensure proper functionality</li>
                                            <li>Keep your PCT codes updated according to FBR requirements</li>
                                            <li>Monitor FBR logs for any communication issues</li>
                                            <li>Backup your system before making major configuration changes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testFbrConnection() {
    const resultDiv = document.getElementById('connection-test-result');
    resultDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing connection...';
    
    FbrApi.checkStatus(function(result) {
        if (result.success && result.online) {
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Connection successful! FBR server is online.</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Connection failed: ' + (result.message || 'Unknown error') + '</div>';
        }
    });
}

function resetSettings() {
    if (confirm('Are you sure you want to reset all settings to default values?')) {
        $('#fbr_pos_enabled').prop('checked', true);
        $('#fbr_pos_auto_send').prop('checked', true);
        $('#fbr_pos_tax_rate').val('17.00');
        $('#fbr_pos_server_url').val('https://fbr.gov.pk/api/pos');
        
        showNotification('Settings reset to defaults. Click "Save Settings" to apply.', 'warning');
    }
}

// Form validation
$('#fbr-settings-form').on('submit', function(e) {
    const taxRate = parseFloat($('#fbr_pos_tax_rate').val());
    const serverUrl = $('#fbr_pos_server_url').val();
    
    if (isNaN(taxRate) || taxRate < 0 || taxRate > 100) {
        e.preventDefault();
        showNotification('Tax rate must be between 0 and 100', 'error');
        return;
    }
    
    if (serverUrl && !isValidUrl(serverUrl)) {
        e.preventDefault();
        showNotification('Please enter a valid FBR server URL', 'error');
        return;
    }
});

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;  
    }
}

// Auto-test connection when URL changes
$('#fbr_pos_server_url').on('blur', function() {
    const url = $(this).val();
    if (url && isValidUrl(url)) {
        setTimeout(testFbrConnection, 500);
    }
});
</script>

<?php init_tail(); ?>