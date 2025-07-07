<div id="guestInvoiceBox" class="guest-invoice-container">
    <button 
        id="guestInvoiceBtn" 
        type="button" 
        class="btn btn-primary" 
             
        
        data-ajax-endpoint="{$ajaxEndpoint}"
        data-csrf_token="{$csrf_token}"
        data-invoice_id="{$invoiceid}"
        data-client_id="{$clientid}"
        data-ajax_action="generate_link">

        <i class="fas fa-share-alt"></i> {$_lang.generate_link}
    </button>
    <div id="guestInvoiceResult" class="result-container"></div>
</div>

<div id="guestInvoiceFormContainer" style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="guestInvoiceModalLabel">
                    <i class="fas fa-link"></i> {$_lang.generate_temporary_access_link}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="guestInvoiceForm"> 
                    <div class="form-group">
                        <label for="guestInvoiceDuration">{$_lang.access_duration}:</label>
                        <select id="guestInvoiceDuration" class="form-control">
                            <option value="1">{$_lang.one_hour}</option>
                            <option value="24" selected>{$_lang.twenty_four_hours}</option>
                            <option value="72">{$_lang.three_days}</option>
                            <option value="168">{$_lang.seven_days}</option>
                            <option value="custom">{$_lang.custom_duration}</option>
                        </select>
                    </div>
                    
                    <div id="guestInvoiceCustomContainer" class="form-group" style="display:none;">
                        <input type="number" id="guestInvoiceCustomHours" class="form-control" 
                               placeholder="{$lang.enter_hours}" min="1" max="720">
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="guestInvoiceSendEmail" checked>
                        <label class="form-check-label" for="guestInvoiceSendEmail">
                            {$_lang.send_link_to_client_via_email}
                        </label>
                    </div>
                    
                    <div id="guestInvoiceModalResult" class="alert" style="display:none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> {$_lang.cancel}
                </button>
                <button type="button" class="btn btn-primary" id="guestInvoiceGenerateBtn">
                    <i class="fas fa-key"></i> {$_lang.generate_link}
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="{$assetPath}css/guestinvoice.css" />
<script type="text/javascript" src="{$assetPath}js/guestlink_modal.js"></script>
{* inline JS moved to external asset *}
{*
{literal}

    $(document).ready(function() {
        // Handle duration selection
        $('#guestInvoiceDuration').change(function() {
            const isCustom = $(this).val() === 'custom';
            $('#guestInvoiceCustomContainer').toggle(isCustom);
            if (isCustom) {
                $('#guestInvoiceCustomHours').focus();
            }
        });
    
        // Generate link handler
        $('#guestInvoiceGenerateBtn').on('click', function() {
            const $btn = $(this);
            const $result = $('#guestInvoiceModalResult');
            const $mainResult = $('#guestInvoiceResult');
            const $form = $('#guestInvoiceForm');
            
            // Reset UI
            $result.hide().removeClass('alert-success alert-danger');
            $mainResult.empty();
            
            // Validate input
            let duration = $('#guestInvoiceDuration').val();
            if (duration === 'custom') {
                duration = parseInt($('#guestInvoiceCustomHours').val());
                if (isNaN(duration) || duration < 1 || duration > 720) {
                    showError($result, 'Please enter a valid duration between 1-720 hours');
                    return;
                }
            }
            
            const sendEmail = $('#guestInvoiceSendEmail').is(':checked');
            const csrf_token = $('#guestInvoiceBtn').data('csrf_token');
            const invoice_id = $('#guestInvoiceBtn').data('invoice_id');
            const client_id = $('#guestInvoiceBtn').data('client_id');
            const ajax_action = $('#guestInvoiceBtn').data('ajax_action');


            const customHours = parseInt($('#guestInvoiceCustomHours').val());
            
            // Prepare AJAX request
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
    
            $.ajax({
                url: $('#guestInvoiceBtn').data('ajax-endpoint'),
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax_action: ajax_action,
                    invoice_id: invoice_id,
                    client_id: client_id,
                    duration: duration === 'custom' ? customHours : duration,
                    send_email: sendEmail,
                    csrf_token: csrf_token
                },
                success: function(response) {
                    if (response.success) {
                        const successHtml = `
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Link Generated</h5>
                                <p>Expires: ${response.expires}</p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="${response.link}" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary copy-btn" type="button">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Close modal and show result below the generate button
                        $('#guestInvoiceModal').modal('hide');
                        $mainResult.html(successHtml);
                        
                        // Initialize copy button
                        $('.copy-btn').click(function() {
                            const $input = $(this).closest('.input-group').find('input');
                            $input.select();
                            document.execCommand('copy');
                            $(this).html('<i class="fas fa-check"></i> Copied!');
                            setTimeout(() => {
                                $(this).html('<i class="fas fa-copy"></i> Copy');
                            }, 2000);
                        });
                    } else {
                        showError($result, response.message || 'Failed to generate link');
                    }
                },
                error: function(xhr) {
                    showError($result, xhr.responseJSON?.message || 'Server communication error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-key"></i> Generate Link');
                }
            });
        });
        
        function showError($element, message) {
            $element.html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${message}
                </div>
            `).addClass('alert-danger').show();
        }
    });

{/literal}
</script>

<style>
.guest-invoice-container {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.result-container {
    margin-top: 15px;
}

#guestInvoiceModal .modal-body {
    padding: 20px;
}

#guestInvoiceModal .form-group {
    margin-bottom: 1.5rem;
}

.copy-btn {
    transition: all 0.3s;
}
</style>*}