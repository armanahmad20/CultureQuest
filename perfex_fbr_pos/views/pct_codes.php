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
                                    <i class="fa fa-barcode"></i> PCT Codes Management
                                </h4>
                                <hr>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#pctCodeModal">
                                    <i class="fa fa-plus"></i> Add New PCT Code
                                </button>
                            </div>
                        </div>
                        
                        <br>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="fa fa-list"></i> PCT Codes List
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <?php if (!empty($pct_codes)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="pct-codes-table">
                                                    <thead>
                                                        <tr>
                                                            <th>PCT Code</th>
                                                            <th>Description</th>
                                                            <th>Tax Rate (%)</th>
                                                            <th>Status</th>
                                                            <th>Created</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($pct_codes as $pct): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo $pct->pct_code; ?></strong>
                                                                </td>
                                                                <td><?php echo $pct->description; ?></td>
                                                                <td>
                                                                    <span class="label label-info">
                                                                        <?php echo number_format($pct->tax_rate, 2); ?>%
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php if ($pct->is_active): ?>
                                                                        <span class="label label-success">Active</span>
                                                                    <?php else: ?>
                                                                        <span class="label label-default">Inactive</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo _dt($pct->created_at); ?></td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-default btn-xs" 
                                                                                onclick="editPctCode(<?php echo $pct->id; ?>)">
                                                                            <i class="fa fa-edit"></i> Edit
                                                                        </button>
                                                                        <button type="button" class="btn btn-danger btn-xs" 
                                                                                onclick="deletePctCode(<?php echo $pct->id; ?>)">
                                                                            <i class="fa fa-trash"></i> Delete
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle fa-2x"></i>
                                                <p>No PCT codes found. Click "Add New PCT Code" to create your first PCT code.</p>
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

<!-- PCT Code Modal -->
<div class="modal fade" id="pctCodeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-barcode"></i> <span id="modal-title">Add New PCT Code</span>
                </h4>
            </div>
            <form id="pct-code-form" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="pct_id" id="pct_id">
                    
                    <div class="form-group">
                        <label for="pct_code">PCT Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pct_code" name="pct_code" required>
                        <small class="form-text text-muted">Enter 8-digit PCT code (e.g., 01111000)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tax_rate">Tax Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                               min="0" max="100" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="is_active" checked>
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save PCT Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#pct-codes-table').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": 5 }
        ]
    });
});

function editPctCode(id) {
    // Get PCT code data via AJAX
    $.ajax({
        url: '<?php echo admin_url('fbr_pos_integration/get_pct_code'); ?>',
        type: 'POST',
        data: { id: id },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                const pct = data.pct_code;
                $('#pct_id').val(pct.id);
                $('#pct_code').val(pct.pct_code);
                $('#description').val(pct.description);
                $('#tax_rate').val(pct.tax_rate);
                $('#is_active').prop('checked', pct.is_active == 1);
                $('#modal-title').text('Edit PCT Code');
                $('#pctCodeModal').modal('show');
            } else {
                alert('Error loading PCT code data');
            }
        },
        error: function() {
            alert('Error loading PCT code data');
        }
    });
}

function deletePctCode(id) {
    if (confirm('Are you sure you want to delete this PCT code?')) {
        $.ajax({
            url: '<?php echo admin_url('fbr_pos_integration/delete_pct_code'); ?>',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting PCT code');
                }
            },
            error: function() {
                alert('Error deleting PCT code');
            }
        });
    }
}

// Reset form when modal is closed
$('#pctCodeModal').on('hidden.bs.modal', function () {
    $('#pct-code-form')[0].reset();
    $('#pct_id').val('');
    $('#modal-title').text('Add New PCT Code');
});
</script>

<?php init_tail(); ?>