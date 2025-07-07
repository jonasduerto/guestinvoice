// Initialize GuestInvoice namespace if it doesn't exist
window.GuestInvoice = window.GuestInvoice || {};
GuestInvoice.lang = (window.APP_CONFIG && APP_CONFIG.lang) || {};
GuestInvoice.t = function(key){ return GuestInvoice.lang[key] || key; };

// Enhanced log details functionality
// Helper to render JSON nicely if server returns just log object
GuestInvoice.renderLogDetails = function(log) {
    if (!log) return '<div class="alert alert-danger">No data</div>';
    let html = '';
    if (log.client_id) {
        html += `<div class="gi-log-section"><h4>${GuestInvoice.t('client_info')}</h4><p><strong>${GuestInvoice.t('client_id')}:</strong> ${log.client_id}</p><p><strong>${GuestInvoice.t('invoice_id')}:</strong> ${log.invoice_id}</p></div>`;
    }
    if (log.request || log.response || log.details || log.data) {
        html += `<div class="gi-log-section"><h4>${GuestInvoice.t('log_details')}</h4><pre>${JSON.stringify(log.request || log.data || log, null, 2)}</pre></div>`;
    }
    return html || '<em>No details</em>';
};

GuestInvoice.toggleLogDetails = function(id, button) {
    // Get the details element
    const details = document.getElementById('log-details-' + id);
    
    // Toggle display
    if (details.style.display === 'none' || !details.style.display) {
        // Hide all other open details first
        document.querySelectorAll('.gi-log-details').forEach(el => {
            if (el.id !== 'log-details-' + id) {
                el.style.display = 'none';
                const otherBtn = el.parentElement.querySelector('button');
                if (otherBtn) {
                    otherBtn.innerHTML = `<i class="fas fa-eye"></i> ${GuestInvoice.t('logs_view_details_btn')}`;
                    otherBtn.classList.remove('active');
                }
            }
        });
        
        // Load details via AJAX first time
        //NOT NEEDED, all details are loaded on page load
        // if (!details.dataset.loaded) {
        //     fetch(window.location.href, {
        //         method: 'POST',
        //         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        //         body: new URLSearchParams({
        //             ajax_action: 'log_details',
        //             log_id: id,
        //             csrf_token: (window.APP_CONFIG && APP_CONFIG.csrf_token) || (document.querySelector('input[name=token]') ? document.querySelector('input[name=token]').value : '')
        //         })
        //     }).then(resp => resp.json())
        //       .then(json => {
        //         if (json.success) {
        //             details.innerHTML = json.html || json.render || GuestInvoice.renderLogDetails(json.log);
        //             details.dataset.loaded = 1;
        //         } else {
        //             details.innerHTML = '<div class="alert alert-danger">' + (json.message || 'Error') + '</div>';
        //         }
        //       }).catch(() => {
        //         details.innerHTML = '<div class="alert alert-danger">' + GuestInvoice.t('server_error') + '</div>';
        //       });
        // }
        // Show current details
        details.style.display = 'block';
        button.innerHTML = '<i class="fas fa-eye-slash"></i> ' + GuestInvoice.t('logs_hide_details_btn');
        button.classList.add('active');
    } else {
        // Hide current details
        details.style.display = 'none';
        button.innerHTML = '<i class="fas fa-eye"></i> ' + GuestInvoice.t('logs_view_details_btn');
        button.classList.remove('active');
    }
};

// Event delegation for dynamically loaded buttons

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.gi-view-details-btn');
    if (btn) {
        e.preventDefault();
        const logId = btn.dataset.logId;
        GuestInvoice.toggleLogDetails(logId, btn);
    }
    
    // Handle clear links button
    const clearLinksBtn = e.target.closest('#clearAllLinksBtn');
    if (clearLinksBtn) {
        e.preventDefault();
        GuestInvoice.clearLogs(clearLinksBtn);
        return;
    }
    
    // Handle clear logs button
    const clearLogsBtn = e.target.closest('#clearLogsBtn');
    if (clearLogsBtn) {
        e.preventDefault();
        GuestInvoice.clearLogs(clearLogsBtn);
        return;
    }
    
    // Handle clear counters button
    const clearCountersBtn = e.target.closest('#clearCountersBtn');
    if (clearCountersBtn) {
        e.preventDefault();
        GuestInvoice.clearLogs(clearCountersBtn);
    }
});

// Handle clear logs button click
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.gi-clearlog-btn');
    if (!btn) return;
    GuestInvoice.clearLogs(btn);
});

// Handle clear counters button click
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.gi-clearcounters-btn');
    if (!btn) return;
    GuestInvoice.clearLogs(btn);
});


GuestInvoice.clearLogs = async function(btn) {
    const $btn = $(btn);
    const originalHtml = $btn.html();
    const token = $btn.data('token');
    const confirmMsg = $btn.data('confirm');
    const successMsg = $btn.data('success');
    const errorMsg = $btn.data('error');
    const ajaxAction = $btn.data('ajax_action');
    
    if (!confirm(confirmMsg)) {
        return;
    }

    // Set loading state
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ' + GuestInvoice.t('clearing'));
    
    try {
        const csrf = APP_CONFIG.csrf_token || (typeof csrf_token !== 'undefined' ? csrf_token : '');
        let url = `${APP_CONFIG.moduleLink}&ajax_action=${ajaxAction}&csrf_token=${encodeURIComponent(csrf)}`;
        const response = await axios.post(url);
        
        if (response.data && response.data.success) {
            alert(successMsg);
            
            // If we're on the dashboard, refresh the dashboard stats
            if (window.location.href.includes('dashboard')) {
                // Force a full page reload to ensure all data is fresh
                location.reload();
            } else {
                // If not on dashboard, just reload the current page
                location.reload();
            }
        } else {
            alert(errorMsg);
        }
    } catch (error) {
        console.error('Error:', ajaxAction, error);
        alert(errorMsg);
    } finally {
        // Reset button state if still on page (reload might have happened on success)
        if (document.body.contains(btn)) {
            $btn.prop('disabled', false).html(originalHtml);
        }
    }
}

// Initialize button states on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.gi-log-details').forEach(el => {
        el.style.display = 'none';
    });
});
