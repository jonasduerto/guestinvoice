<div class="gi-admin-container">
    <!-- Statistics Cards -->
    <div class="gi-stats-grid" style="margin-bottom: 20px;">
        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-link"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$activeLinks}</h3>
                <p>{$_lang.activelinks}</p>
            </div>
        </div>

        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$totalAccesses}</h3>
                <p>{$_lang.totalaccesses}</p>
            </div>
        </div>

        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$expiredLinks}</h3>
                <p>{$_lang.expiredlinks}</p>
            </div>
        </div>
    </div>

    <div class="gi-card">
        <div class="gi-card-header">
            <h3><i class="fas fa-history"></i> {$_lang.activity_history}</h3>
            <div class="gi-card-actions">
                <button id="clearAllLinksBtn"
                type="button"
                class="gi-btn gi-btn-danger gi-btn-sm"
                data-token="{$csrfToken}"
                data-confirm="{$_lang.confirm_clear_links_message}"
                data-success="{$_lang.links_cleared}"
                data-error="{$_lang.error_clearing_links}"
                data-ajax_action="clear_all_links">
                    <i class="fas fa-trash-alt"></i> {$_lang.clear_all_links}
                </button>
                <button id="clearCountersBtn"
                type="button"
                class="gi-btn gi-btn-warning gi-clearcounters-btn gi-btn-sm"
                data-token="{$csrfToken}"
                data-confirm="{$_lang.confirm_clear_counters_message}"
                data-success="{$_lang.counters_cleared}"
                data-error="{$_lang.error_clearing_counters}"
                data-ajax_action="clear_counters">
                    <i class="fas fa-sync-alt"></i> {$_lang.clear_counters}
                </button>
                <button id="clearLogsBtn"
                type="button"
                class="gi-btn gi-btn-secondary gi-clearlog-btn gi-btn-sm"
                data-token="{$csrfToken}"
                data-confirm="{$_lang.confirm_clear_logs_message}"
                data-success="{$_lang.logs_cleared}"
                data-error="{$_lang.error_clearing_logs}"
                data-ajax_action="clear_logs">
                    <i class="fas fa-trash"></i> {$_lang.clear_logs}
                </button>
            </div>
        </div>
        
        <div class="gi-card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="activityLogsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{$_lang.activityHistory_date}</th>
                            <th>{$_lang.activityHistory_action}</th>
                            <th>{$_lang.activityHistory_client}</th>
                            <th>{$_lang.activityHistory_invoice_id}</th>
                            <th>{$_lang.actions}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if !empty($activity_logs)}
                            {foreach from=$activity_logs item=log}
                            <tr>
                                <td>{$log.id}</td>
                                <td data-order="{$log.created_at|strtotime}">{$log.created_at|date_format:"%Y-%m-%d %H:%M:%S"}</td>
                                <td>{$log.action}</td>
                                <td>{$log.client_name|default:'-'}</td>
                                <td>{$log.invoice_id|default:'-'}</td>
                                <td>
                                    <button class="gi-btn gi-btn-sm gi-btn-info gi-view-details-btn" 
                                            data-log-id="{$log.id}">
                                        <i class="fas fa-eye"></i> {$_lang.logs_view_details_btn}
                                    </button>
                                </td>
                            </tr>
                            <tr class="gi-log-details" id="log-details-{$log.id}" style="display: none;">
                                <td colspan="6">
                                    <div class="gi-log-details-content">
                                        {if $log.details}
                                            <pre>{$log.details|json_encode:128|escape}</pre>
                                        {else}
                                            <p>No details available</p>
                                        {/if}
                                    </div>
                                </td>
                            </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Guest Invoice Links Table -->
    <div class="gi-card" style="margin-top: 20px;">
        <div class="gi-card-header">
            <h3><i class="fas fa-link"></i> {$_lang.guest_links}</h3>
        </div>
        <div class="gi-card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="guestLinksTable">
                    <thead>
                        <tr>
                            <th>{$_lang.invoice_id}</th>
                            <th>{$_lang.client}</th>
                            <th>{$_lang.company}</th>
                            <th>{$_lang.created}</th>
                            <th>{$_lang.expires}</th>
                            <th>{$_lang.status}</th>
                            <th>{$_lang.access_count}</th>
                            <th>{$_lang.actions}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if !empty($guest_links)}
                            {foreach from=$guest_links item=link}
                            <tr>
                                <td data-order="{$link.invoice_id}">#{$link.invoice_id}</td>
                                <td>{$link.client_name}</td>
                                <td>{$link.company_name}</td>
                                <td data-order="{$link.created_at|strtotime}">{$link.created_at|date_format:"%Y-%m-%d"}</td>
                                <td data-order="{$link.expires_at|strtotime}">{$link.expires_at|date_format:"%Y-%m-%d"}</td>
                                <td data-order="{$link.is_active}">
                                    {if $link.is_active}
                                        <span class="gi-badge gi-badge-success">{$_lang.active}</span>
                                    {else}
                                        <span class="gi-badge gi-badge-danger">{$_lang.expired}</span>
                                    {/if}
                                </td>
                                <td data-order="{$link.access_count}">{$link.access_count}</td>
                                <td>
                                    <a href="viewinvoice.php?id={$link.invoice_id}" class="gi-btn gi-btn-sm gi-btn-info" target="_blank" title="{$_lang.view_invoice}">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <button class="gi-btn gi-btn-sm gi-btn-secondary gi-copy-link" data-link="{$link.referral_link}" title="{$_lang.copy_link}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add DataTables CSS and JS -->
{* <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/> *}
{* <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css"/> *}
{* <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> *}
{* <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script> *}
{* <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script> *}
{* <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script> *}
<!-- Add clipboard.js for copy functionality -->
<style>
/* Custom styles for DataTables */
.dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
    display: inline-block;
    width: auto;
}

.dataTables_wrapper .dataTables_length select {
    width: auto;
    display: inline-block;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.25em 0.75em;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current, 
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0d6efd;
    color: white !important;
    border-color: #0d6efd;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
    border-color: #dee2e6;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 0.85em;
}
</style>