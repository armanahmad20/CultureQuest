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
                                    <i class="fa fa-list-alt"></i> FBR Communication Logs
                                </h4>
                                <hr>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php if (!empty($logs)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped fbr-data-table" id="fbr-logs-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date/Time</th>
                                                            <th>Invoice</th>
                                                            <th>Store</th>
                                                            <th>Action</th>
                                                            <th>Status</th>
                                                            <th>FBR Invoice</th>
                                                            <th>Response</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($logs as $log): ?>
                                                            <tr>
                                                                <td>
                                                                    <small><?php echo _dt($log->created_at); ?></small>
                                                                </td>
                                                                <td>
                                                                    <?php if ($log->invoice_id): ?>
                                                                        <a href="<?php echo admin_url('invoices/list_invoices/' . $log->invoice_id); ?>" target="_blank">
                                                                            #<?php echo $log->invoice_id; ?>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <small><?php echo $log->store_config_id ? 'Store #' . $log->store_config_id : '-'; ?></small>
                                                                </td>
                                                                <td>
                                                                    <span class="fbr-badge primary">
                                                                        <?php echo ucwords(str_replace('_', ' ', $log->action)); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php if ($log->status == 'success'): ?>
                                                                        <span class="fbr-badge success">Success</span>
                                                                    <?php elseif ($log->status == 'error'): ?>
                                                                        <span class="fbr-badge danger">Error</span>
                                                                    <?php else: ?>
                                                                        <span class="fbr-badge warning">Pending</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($log->fbr_invoice_number): ?>
                                                                        <span class="fbr-invoice-number"><?php echo $log->fbr_invoice_number; ?></span>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($log->error_message): ?>
                                                                        <span class="text-danger" title="<?php echo htmlentities($log->error_message); ?>">
                                                                            <i class="fa fa-exclamation-triangle"></i> Error
                                                                        </span>
                                                                    <?php elseif ($log->response_data): ?>
                                                                        <span class="text-success" title="Response received">
                                                                            <i class="fa fa-check-circle"></i> Success
                                                                        </span>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-default btn-xs" 
                                                                                onclick="viewLogDetails(<?php echo $log->id; ?>)">
                                                                            <i class="fa fa-eye"></i> View
                                                                        </button>
                                                                        <?php if ($log->status == 'error' && $log->invoice_id): ?>
                                                                            <button type="button" class="btn btn-warning btn-xs" 
                                                                                    onclick="retryFbrInvoice(<?php echo $log->invoice_id; ?>)">
                                                                                <i class="fa fa-refresh"></i> Retry
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="fbr-empty-state">
                                                <i class="fa fa-list-alt"></i>
                                                <h4>No FBR Logs Found</h4>
                                                <p>No communication logs found. FBR logs will appear here when invoices are sent to FBR.</p>
                                            </div>
                                        <?php endif; ?>
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

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-info-circle"></i> FBR Log Details
                </h4>
            </div>
            <div class="modal-body">
                <div id="log-details-content">
                    <div class="fbr-loading">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p>Loading log details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#fbr-logs-table').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": 7 }
        ]
    });
});

function viewLogDetails(logId) {
    $('#logDetailsModal').modal('show');
    
    $.ajax({
        url: '<?php echo admin_url('fbr_pos_integration/get_log_details'); ?>',
        type: 'POST',
        data: { log_id: logId },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                displayLogDetails(data.log);
            } else {
                $('#log-details-content').html('<div class="alert alert-danger">Error loading log details</div>');
            }
        },
        error: function() {
            $('#log-details-content').html('<div class="alert alert-danger">Error loading log details</div>');
        }
    });
}

function displayLogDetails(log) {
    let requestData = '';
    let responseData = '';
    
    if (log.request_data) {
        try {
            const parsedRequest = JSON.parse(log.request_data);
            requestData = '<pre>' + JSON.stringify(parsedRequest, null, 2) + '</pre>';
        } catch (e) {
            requestData = '<pre>' + log.request_data + '</pre>';
        }
    }
    
    if (log.response_data) {
        try {
            const parsedResponse = JSON.parse(log.response_data);
            responseData = '<pre>' + JSON.stringify(parsedResponse, null, 2) + '</pre>';
        } catch (e) {
            responseData = '<pre>' + log.response_data + '</pre>';
        }
    }
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h5>Basic Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Log ID</th>
                        <td>${log.id}</td>
                    </tr>
                    <tr>
                        <th>Date/Time</th>
                        <td>${log.created_at}</td>
                    </tr>
                    <tr>
                        <th>Invoice ID</th>
                        <td>${log.invoice_id || '-'}</td>
                    </tr>
                    <tr>
                        <th>Store Config ID</th>
                        <td>${log.store_config_id || '-'}</td>
                    </tr>
                    <tr>
                        <th>Action</th>
                        <td><span class="fbr-badge primary">${log.action}</span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            ${log.status === 'success' ? '<span class="fbr-badge success">Success</span>' :
                              log.status === 'error' ? '<span class="fbr-badge danger">Error</span>' :
                              '<span class="fbr-badge warning">Pending</span>'}
                        </td>
                    </tr>
                    <tr>
                        <th>FBR Invoice Number</th>
                        <td>${log.fbr_invoice_number || '-'}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Error Information</h5>
                ${log.error_message ? 
                    '<div class="alert alert-danger"><strong>Error:</strong><br>' + log.error_message + '</div>' :
                    '<div class="alert alert-success">No errors reported</div>'
                }
            </div>
        </div>
        
        ${requestData ? `
            <div class="row">
                <div class="col-md-12">
                    <h5>Request Data</h5>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            ${requestData}
                        </div>
                    </div>
                </div>
            </div>
        ` : ''}
        
        ${responseData ? `
            <div class="row">
                <div class="col-md-12">
                    <h5>Response Data</h5>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            ${responseData}
                        </div>
                    </div>
                </div>
            </div>
        ` : ''}
    `;
    
    $('#log-details-content').html(html);
}

function retryFbrInvoice(invoiceId) {
    if (confirm('Are you sure you want to retry sending this invoice to FBR?')) {
        FbrApi.retryInvoice(invoiceId, function(result) {
            if (result.success) {
                location.reload();
            }
        });
    }
}
</script>

<?php init_tail(); ?>