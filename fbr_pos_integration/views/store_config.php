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
                                    <i class="fa fa-store"></i> Store Configuration
                                </h4>
                                <hr>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-warning" onclick="testDbConnection()">
                                    <i class="fa fa-database"></i> Test Database
                                </button>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#storeConfigModal">
                                    <i class="fa fa-plus"></i> Add New Store Configuration
                                </button>
                            </div>
                        </div>
                        
                        <br>
                        
                        <div class="row">
                            <?php if (!empty($store_configs)): ?>
                                <?php foreach ($store_configs as $config): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="fbr-config-card <?php echo $config->is_active ? 'active' : ''; ?>" id="store-config-<?php echo $config->id; ?>">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5><?php echo $config->store_name; ?></h5>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <span class="store-config-status">
                                                        <?php if ($config->is_active): ?>
                                                            <span class="label label-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Inactive</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p><strong>Store ID:</strong> <?php echo $config->store_id; ?></p>
                                                    <p><strong>NTN:</strong> <?php echo $config->ntn; ?></p>
                                                    <p><strong>STRN:</strong> <?php echo $config->strn; ?></p>
                                                    <p><strong>POS Type:</strong> <?php echo $config->pos_type; ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="fbr-action-buttons">
                                                <?php if (!$config->is_active): ?>
                                                    <button type="button" class="btn btn-success btn-sm" onclick="activateStoreConfig(<?php echo $config->id; ?>)">
                                                        <i class="fa fa-check"></i> Activate
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-default btn-sm" onclick="editStoreConfig(<?php echo $config->id; ?>)">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteStoreConfig(<?php echo $config->id; ?>)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-md-12">
                                    <div class="fbr-empty-state">
                                        <i class="fa fa-store"></i>
                                        <h4>No Store Configurations</h4>
                                        <p>Click "Add New Store Configuration" to create your first store configuration.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Store Configuration Modal -->
<div class="modal fade" id="storeConfigModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-store"></i> <span id="modal-title">Add New Store Configuration</span>
                </h4>
            </div>
            <form id="store-config-form" method="POST">
                <div class="modal-body">
                    <div class="fbr-form-errors"></div>
                    
                    <input type="hidden" name="config_id" id="config_id">
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    
                    <div class="fbr-form-section">
                        <h4>Store Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_name">Store Name <span class="fbr-required-field">*</span></label>
                                    <input type="text" class="form-control" id="store_name" name="store_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_id">Store ID <span class="fbr-required-field">*</span></label>
                                    <input type="text" class="form-control" id="store_id" name="store_id" required>
                                    <small class="fbr-help-text">Store ID provided by FBR</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Store Address <span class="fbr-required-field">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    
                    <div class="fbr-form-section">
                        <h4>Tax Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ntn">NTN (National Tax Number) <span class="fbr-required-field">*</span></label>
                                    <input type="text" class="form-control" id="ntn" name="ntn" maxlength="13" required>
                                    <small class="fbr-help-text">13-digit National Tax Number</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="strn">STRN (Sales Tax Registration Number) <span class="fbr-required-field">*</span></label>
                                    <input type="text" class="form-control" id="strn" name="strn" required>
                                    <small class="fbr-help-text">Sales Tax Registration Number</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fbr-form-section">
                        <h4>POS System Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pos_type">POS Type</label>
                                    <input type="text" class="form-control" id="pos_type" name="pos_type" value="Perfex CRM">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pos_version">POS Version</label>
                                    <input type="text" class="form-control" id="pos_version" name="pos_version" value="1.0.0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="ip_address">IP Address</label>
                            <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="192.168.1.100">
                            <small class="fbr-help-text">IP address of the POS system</small>
                        </div>
                    </div>
                    
                    <div class="fbr-form-section">
                        <h4>FBR SDC Connection</h4>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Note:</strong> FBR SDC runs on Windows machines. Configure the URL to reach your FBR SDC installation.
                        </div>
                        
                        <div class="form-group">
                            <label for="sdc_url">FBR SDC URL <span class="fbr-required-field">*</span></label>
                            <input type="text" class="form-control" id="sdc_url" name="sdc_url" value="http://localhost:8080" placeholder="http://192.168.1.100:8080">
                            <small class="fbr-help-text">URL to connect to FBR Sales Data Controller</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sdc_username">SDC Username</label>
                                    <input type="text" class="form-control" id="sdc_username" name="sdc_username">
                                    <small class="fbr-help-text">Username for SDC authentication (if required)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sdc_password">SDC Password</label>
                                    <input type="password" class="form-control" id="sdc_password" name="sdc_password">
                                    <small class="fbr-help-text">Password for SDC authentication (if required)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="is_active">
                                Set as Active Configuration
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editStoreConfig(id) {
    // Get store config data via AJAX
    $.ajax({
        url: '<?php echo admin_url('fbr_pos_integration/get_store_config'); ?>',
        type: 'POST',
        data: { id: id },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                const config = data.config;
                $('#config_id').val(config.id);
                $('#store_name').val(config.store_name);
                $('#store_id').val(config.store_id);
                $('#ntn').val(config.ntn);
                $('#strn').val(config.strn);
                $('#address').val(config.address);
                $('#pos_type').val(config.pos_type);
                $('#pos_version').val(config.pos_version);
                $('#ip_address').val(config.ip_address);
                $('#sdc_url').val(config.sdc_url || 'http://localhost:8080');
                $('#sdc_username').val(config.sdc_username || '');
                $('#sdc_password').val(config.sdc_password || '');
                $('#is_active').prop('checked', config.is_active == 1);
                $('#modal-title').text('Edit Store Configuration');
                $('#storeConfigModal').modal('show');
            } else {
                alert('Error loading store configuration data');
            }
        },
        error: function() {
            alert('Error loading store configuration data');
        }
    });
}

function activateStoreConfig(id) {
    if (confirm('Are you sure you want to activate this store configuration?')) {
        StoreConfigManager.activate(id, function(result) {
            if (result.success) {
                location.reload();
            }
        });
    }
}

function deleteStoreConfig(id) {
    StoreConfigManager.delete(id);
}

function testDbConnection() {
    $.ajax({
        url: admin_url + 'fbr_pos_integration/test_db_connection',
        type: 'POST',
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    let message = 'Database Test Results:\n\n';
                    message += 'Table exists: ' + (result.table_exists ? 'YES' : 'NO') + '\n';
                    message += 'Record count: ' + result.record_count + '\n';
                    message += 'Configs found: ' + result.configs_found + '\n';
                    if (result.configs && result.configs.length > 0) {
                        message += '\nConfigurations:\n';
                        result.configs.forEach(function(config, index) {
                            message += (index + 1) + '. ' + config.store_name + ' (ID: ' + config.id + ')\n';
                        });
                    }
                    alert(message);
                } else {
                    alert('Database test failed: ' + result.message);
                }
            } catch (e) {
                alert('Error parsing database test response');
            }
        },
        error: function(xhr, status, error) {
            alert('Error testing database connection: ' + error);
        }
    });
}

// Form validation and submission
$('#store-config-form').on('submit', function(e) {
    e.preventDefault();
    
    const errors = FbrFormValidator.validateStoreConfig(this);
    if (!FbrFormValidator.showErrors(errors)) {
        return;
    }
    
    // Submit via AJAX with proper CSRF token
    const formData = $(this).serialize();
    
    $.ajax({
        url: admin_url + 'fbr_pos_integration/save_store_config',
        type: 'POST',
        data: formData,
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    showNotification('Store configuration saved successfully!', 'success');
                    $('#storeConfigModal').modal('hide');
                    location.reload();
                } else {
                    showNotification('Error: ' + result.message, 'error');
                }
            } catch (e) {
                showNotification('Error saving store configuration', 'error');
            }
        },
        error: function(xhr, status, error) {
            if (xhr.status === 419) {
                showNotification('Session expired. Please refresh the page and try again.', 'error');
            } else {
                showNotification('Error saving store configuration: ' + error, 'error');
            }
        }
    });
});

// Reset form when modal is closed
$('#storeConfigModal').on('hidden.bs.modal', function () {
    $('#store-config-form')[0].reset();
    $('#config_id').val('');
    $('#modal-title').text('Add New Store Configuration');
    $('.fbr-form-errors').empty();
});

// NTN input formatting
$('#ntn').on('input', function() {
    const value = $(this).val().replace(/\D/g, '');
    if (value.length <= 13) {
        $(this).val(value);
    }
});
</script>

<?php init_tail(); ?>