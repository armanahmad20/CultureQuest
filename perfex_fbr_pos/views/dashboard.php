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
                                    <i class="fa fa-dashboard"></i> FBR POS Integration Dashboard
                                </h4>
                                <hr>
                            </div>
                        </div>
                        
                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="panel panel-primary">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-file-text-o fa-3x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"><?php echo $stats['total_invoices']; ?></div>
                                                <div>Total Invoices</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="panel panel-success">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-check-circle fa-3x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"><?php echo $stats['confirmed_invoices']; ?></div>
                                                <div>Confirmed by FBR</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-clock-o fa-3x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"><?php echo $stats['pending_invoices']; ?></div>
                                                <div>Pending FBR</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="panel panel-danger">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-times-circle fa-3x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"><?php echo $stats['failed_invoices']; ?></div>
                                                <div>Failed</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Revenue and Status -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="fa fa-money"></i> Revenue Overview
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h2 class="text-success">
                                                <?php echo app_format_money($stats['total_revenue'], get_base_currency()); ?>
                                            </h2>
                                            <p class="text-muted">Total Confirmed Revenue</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="fa fa-server"></i> FBR Server Status
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div id="fbr-status-indicator">
                                                <i class="fa fa-spinner fa-spin"></i> Checking...
                                            </div>
                                            <button type="button" class="btn btn-default btn-sm" onclick="checkFbrStatus()">
                                                <i class="fa fa-refresh"></i> Check Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Activity -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="fa fa-activity"></i> Recent FBR Activities
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <?php if (!empty($recent_logs)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Invoice</th>
                                                            <th>Action</th>
                                                            <th>Status</th>
                                                            <th>Message</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_logs as $log): ?>
                                                            <tr>
                                                                <td><?php echo _dt($log->created_at); ?></td>
                                                                <td>#<?php echo $log->invoice_id; ?></td>
                                                                <td>
                                                                    <span class="label label-default">
                                                                        <?php echo ucwords(str_replace('_', ' ', $log->action)); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php if ($log->status == 'success'): ?>
                                                                        <span class="label label-success">Success</span>
                                                                    <?php elseif ($log->status == 'error'): ?>
                                                                        <span class="label label-danger">Error</span>
                                                                    <?php else: ?>
                                                                        <span class="label label-warning">Pending</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($log->error_message): ?>
                                                                        <span class="text-danger"><?php echo $log->error_message; ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-success">Processed successfully</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle fa-2x"></i>
                                                <p>No recent FBR activities found.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Store Configuration -->
                        <?php if ($active_store): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">
                                                <i class="fa fa-store"></i> Active Store Configuration
                                            </h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Store Name:</strong> <?php echo $active_store->store_name; ?></p>
                                                    <p><strong>Store ID:</strong> <?php echo $active_store->store_id; ?></p>
                                                    <p><strong>NTN:</strong> <?php echo $active_store->ntn; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>STRN:</strong> <?php echo $active_store->strn; ?></p>
                                                    <p><strong>POS Type:</strong> <?php echo $active_store->pos_type; ?></p>
                                                    <p><strong>POS Version:</strong> <?php echo $active_store->pos_version; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-warning"></i>
                                        <strong>Warning:</strong> No active store configuration found. 
                                        <a href="<?php echo admin_url('fbr_pos_integration/store_config'); ?>">
                                            Configure store settings
                                        </a> to enable FBR integration.
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkFbrStatus() {
    const indicator = document.getElementById('fbr-status-indicator');
    indicator.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Checking...';
    
    $.ajax({
        url: '<?php echo admin_url('fbr_pos_integration/check_status'); ?>',
        type: 'POST',
        success: function(response) {
            const data = JSON.parse(response);
            let statusHtml = '';
            
            if (data.online) {
                statusHtml = '<span class="text-success"><i class="fa fa-check-circle"></i> FBR Server Online</span>';
            } else {
                statusHtml = '<span class="text-danger"><i class="fa fa-times-circle"></i> FBR Server Offline</span>';
            }
            
            indicator.innerHTML = statusHtml;
        },
        error: function() {
            indicator.innerHTML = '<span class="text-danger"><i class="fa fa-times-circle"></i> Connection Error</span>';
        }
    });
}

// Check status on page load
$(document).ready(function() {
    checkFbrStatus();
});
</script>

<?php init_tail(); ?>