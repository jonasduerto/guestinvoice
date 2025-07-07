$(document).ready(function () {
    // Show form on button click
    $('#guestInvoiceBtn').on('click', function () {
        $(this).hide();
        $('#guestInvoiceResult').empty();
        $('#guestInvoiceFormContainer').show();
    });

    // Cancel button hides form and shows main button
    $(document).on('click', '#guestInvoiceCancelBtn', function () {
        $('#guestInvoiceFormContainer').hide();
        $('#guestInvoiceBtn').show();
        $('#guestInvoiceModalResult').hide();
    });
    // Handle duration selection
    $('#guestInvoiceDuration').change(function () {
        const isCustom = $(this).val() === 'custom';
        $('#guestInvoiceCustomContainer').toggle(isCustom);
        if (isCustom) {
            $('#guestInvoiceCustomHours').focus();
        }
    });

    // Generate link handler
    $('#guestInvoiceGenerateBtn').on('click', function () {
        const $btn = $(this);
        const $result = $('#guestInvoiceModalResult');
        const $mainResult = $('#guestInvoiceResult');

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
            success: function (response) {
                if (response.success) {
                    const successHtml = `
                        <div class="d-flex align-items-center gi-link-result shadow-sm" 
                        style="background: #dff9df;border:1px solid #28a74530;border-radius:8px;padding:12px;gap:10px;width:100%;max-width: 100%;margin: 0 auto;display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                            
                            <div class="input-group w-100" style="position: relative;display: flex;width: 100%;border-collapse: separate; gap: 10px;">
                                <input type="text" class="form-control" style="flex:1 1 auto;min-width:0;min-height: 36px;" value="${response.link}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-primary copy-btn" type="button">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Expires: ${response.expires}</small>
                    `;

                    // Close modal and show result below the generate button
                    $('#guestInvoiceFormContainer').hide();
                    $mainResult.html(successHtml);

                    // Initialize copy button
                    $('.copy-btn').click(function () {
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
            error: function (xhr) {
                showError($result, xhr.responseJSON?.message || 'Server communication error');
            },
            complete: function () {
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
