/* FBR POS Integration JavaScript */

(function() {
    'use strict';

    // Initialize FBR POS module
    $(document).ready(function() {
        initializeFbrPos();
    });

    function initializeFbrPos() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize popovers
        $('[data-toggle="popover"]').popover();
        
        // Auto-refresh FBR status
        if (typeof fbrAutoRefresh !== 'undefined' && fbrAutoRefresh) {
            setInterval(refreshFbrStatus, 30000); // Refresh every 30 seconds
        }
        
        // Initialize real-time notifications
        initializeNotifications();
    }

    // FBR API Functions
    window.FbrApi = {
        sendInvoice: function(invoiceId, callback) {
            showLoader();
            $.ajax({
                url: admin_url + 'fbr_pos_integration/send_invoice',
                type: 'POST',
                data: { invoice_id: invoiceId },
                success: function(response) {
                    hideLoader();
                    const data = JSON.parse(response);
                    if (callback) callback(data);
                    
                    if (data.success) {
                        showNotification('Invoice sent to FBR successfully!', 'success');
                    } else {
                        showNotification('Failed to send invoice to FBR: ' + data.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    showNotification('Error connecting to FBR servers', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        },

        checkStatus: function(callback) {
            $.ajax({
                url: admin_url + 'fbr_pos_integration/check_status',
                type: 'POST',
                success: function(response) {
                    const data = JSON.parse(response);
                    if (callback) callback(data);
                    updateStatusIndicator(data);
                },
                error: function() {
                    const errorData = { success: false, online: false, message: 'Connection error' };
                    if (callback) callback(errorData);
                    updateStatusIndicator(errorData);
                }
            });
        },

        retryInvoice: function(invoiceId, callback) {
            showLoader();
            $.ajax({
                url: admin_url + 'fbr_pos_integration/retry_invoice',
                type: 'POST',
                data: { invoice_id: invoiceId },
                success: function(response) {
                    hideLoader();
                    const data = JSON.parse(response);
                    if (callback) callback(data);
                    
                    if (data.success) {
                        showNotification('Invoice retry successful!', 'success');
                    } else {
                        showNotification('Retry failed: ' + data.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    showNotification('Error retrying invoice', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        }
    };

    // UI Helper Functions
    function showLoader() {
        if ($('#fbr-loader').length === 0) {
            $('body').append('<div id="fbr-loader" class="fbr-loading"><i class="fa fa-spinner fa-spin"></i><p>Processing...</p></div>');
        }
        $('#fbr-loader').show();
    }

    function hideLoader() {
        $('#fbr-loader').hide();
    }

    function showNotification(message, type) {
        const notification = $('<div class="fbr-notification ' + type + '">' + message + '</div>');
        $('body').append(notification);
        
        // Auto-hide notification after 5 seconds
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    function updateStatusIndicator(data) {
        const indicator = $('#fbr-status-indicator');
        if (indicator.length === 0) return;
        
        let statusHtml = '';
        if (data.online) {
            statusHtml = '<span class="fbr-server-status online"><i class="fa fa-check-circle"></i> FBR Server Online</span>';
        } else {
            statusHtml = '<span class="fbr-server-status offline"><i class="fa fa-times-circle"></i> FBR Server Offline</span>';
        }
        
        indicator.html(statusHtml);
    }

    function refreshFbrStatus() {
        FbrApi.checkStatus(function(data) {
            // Update any status indicators on the page
            updateStatusIndicator(data);
        });
    }

    function initializeNotifications() {
        // Initialize WebSocket or polling for real-time updates
        // This would be implemented based on your specific needs
    }

    // PCT Code Functions
    window.PctCodeManager = {
        add: function(data, callback) {
            $.ajax({
                url: admin_url + 'fbr_pos_integration/add_pct_code',
                type: 'POST',
                data: data,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (callback) callback(result);
                    
                    if (result.success) {
                        showNotification('PCT code added successfully!', 'success');
                    } else {
                        showNotification('Failed to add PCT code: ' + result.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error adding PCT code', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        },

        edit: function(id, data, callback) {
            $.ajax({
                url: admin_url + 'fbr_pos_integration/edit_pct_code',
                type: 'POST',
                data: $.extend({ id: id }, data),
                success: function(response) {
                    const result = JSON.parse(response);
                    if (callback) callback(result);
                    
                    if (result.success) {
                        showNotification('PCT code updated successfully!', 'success');
                    } else {
                        showNotification('Failed to update PCT code: ' + result.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error updating PCT code', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        },

        delete: function(id, callback) {
            if (!confirm('Are you sure you want to delete this PCT code?')) {
                return;
            }
            
            $.ajax({
                url: admin_url + 'fbr_pos_integration/delete_pct_code',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (callback) callback(result);
                    
                    if (result.success) {
                        showNotification('PCT code deleted successfully!', 'success');
                        // Remove the row from the table
                        $('#pct-code-' + id).fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        showNotification('Failed to delete PCT code: ' + result.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error deleting PCT code', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        }
    };

    // Store Configuration Functions
    window.StoreConfigManager = {
        activate: function(configId, callback) {
            $.ajax({
                url: admin_url + 'fbr_pos_integration/activate_store_config',
                type: 'POST',
                data: { config_id: configId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (callback) callback(result);
                    
                    if (result.success) {
                        showNotification('Store configuration activated successfully!', 'success');
                        // Update UI to reflect the change
                        updateStoreConfigUI(configId);
                    } else {
                        showNotification('Failed to activate store configuration: ' + result.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error activating store configuration', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        },

        delete: function(configId, callback) {
            if (!confirm('Are you sure you want to delete this store configuration?')) {
                return;
            }
            
            $.ajax({
                url: admin_url + 'fbr_pos_integration/delete_store_config',
                type: 'POST',
                data: { id: configId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (callback) callback(result);
                    
                    if (result.success) {
                        showNotification('Store configuration deleted successfully!', 'success');
                        // Remove the config card
                        $('#store-config-' + configId).fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        showNotification('Failed to delete store configuration: ' + result.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error deleting store configuration', 'error');
                    if (callback) callback({ success: false, message: 'Connection error' });
                }
            });
        }
    };

    function updateStoreConfigUI(activeConfigId) {
        // Remove active class from all config cards
        $('.fbr-config-card').removeClass('active');
        
        // Add active class to the selected config
        $('#store-config-' + activeConfigId).addClass('active');
        
        // Update active status indicators
        $('.store-config-status').html('<span class="label label-default">Inactive</span>');
        $('#store-config-' + activeConfigId + ' .store-config-status').html('<span class="label label-success">Active</span>');
    }

    // Utility Functions
    window.FbrUtils = {
        formatCurrency: function(amount) {
            return 'PKR ' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        formatDate: function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        },

        validateNTN: function(ntn) {
            // Pakistani NTN validation (13 digits)
            const ntnPattern = /^\d{13}$/;
            return ntnPattern.test(ntn);
        },

        validateSTRN: function(strn) {
            // Pakistani STRN validation
            const strnPattern = /^[A-Z0-9]{10,15}$/;
            return strnPattern.test(strn);
        },

        validatePctCode: function(pctCode) {
            // PCT code validation (8 digits)
            const pctPattern = /^\d{8}$/;
            return pctPattern.test(pctCode);
        },

        generateQrCode: function(data, containerId) {
            // Generate QR code using a library like QRCode.js
            if (typeof QRCode !== 'undefined') {
                const qr = new QRCode(document.getElementById(containerId), {
                    text: data,
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                // Fallback: display data as text
                $('#' + containerId).html('<div class="fbr-qr-fallback"><strong>QR Data:</strong><br>' + data + '</div>');
            }
        }
    };

    // Form Validation
    window.FbrFormValidator = {
        validateStoreConfig: function(form) {
            const errors = [];
            
            const storeName = $(form).find('[name="store_name"]').val();
            if (!storeName || storeName.trim() === '') {
                errors.push('Store name is required');
            }
            
            const storeId = $(form).find('[name="store_id"]').val();
            if (!storeId || storeId.trim() === '') {
                errors.push('Store ID is required');
            }
            
            const ntn = $(form).find('[name="ntn"]').val();
            if (!FbrUtils.validateNTN(ntn)) {
                errors.push('NTN must be exactly 13 digits');
            }
            
            const strn = $(form).find('[name="strn"]').val();
            if (!FbrUtils.validateSTRN(strn)) {
                errors.push('STRN format is invalid');
            }
            
            return errors;
        },

        validatePctCode: function(form) {
            const errors = [];
            
            const pctCode = $(form).find('[name="pct_code"]').val();
            if (!FbrUtils.validatePctCode(pctCode)) {
                errors.push('PCT code must be exactly 8 digits');
            }
            
            const description = $(form).find('[name="description"]').val();
            if (!description || description.trim() === '') {
                errors.push('Description is required');
            }
            
            const taxRate = parseFloat($(form).find('[name="tax_rate"]').val());
            if (isNaN(taxRate) || taxRate < 0 || taxRate > 100) {
                errors.push('Tax rate must be between 0 and 100');
            }
            
            return errors;
        },

        showErrors: function(errors) {
            if (errors.length > 0) {
                let errorHtml = '<div class="alert alert-danger"><ul>';
                errors.forEach(function(error) {
                    errorHtml += '<li>' + error + '</li>';
                });
                errorHtml += '</ul></div>';
                
                $('.fbr-form-errors').html(errorHtml);
                return false;
            }
            
            $('.fbr-form-errors').empty();
            return true;
        }
    };

    // Export global functions for backward compatibility
    window.checkFbrStatus = function() {
        FbrApi.checkStatus();
    };

    window.sendInvoiceToFbr = function(invoiceId) {
        FbrApi.sendInvoice(invoiceId);
    };

    window.retryFbrInvoice = function(invoiceId) {
        FbrApi.retryInvoice(invoiceId);
    };

    window.editPctCode = function(id) {
        // This function would be implemented based on your specific modal/form setup
        console.log('Edit PCT code:', id);
    };

    window.deletePctCode = function(id) {
        PctCodeManager.delete(id);
    };

    window.activateStoreConfig = function(configId) {
        StoreConfigManager.activate(configId);
    };

    window.deleteStoreConfig = function(configId) {
        StoreConfigManager.delete(configId);
    };

})();