<main class="gi-admin" id="wrapguestinvoice">     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{$assetPath}css/guestinvoice.css" />
    <script>
        // Configuración inicial pasada desde PHP
        const APP_CONFIG = {
            assetPath: "{$assetPath}",
            moduleLink: "{$modulelink}",
            currentView: "{$currentPage|default:'dashboard'}",
            csrf_token: "{$csrfToken}",
            lang: {
                loading: "{$_lang.loading}",
                errorLoading: "{$_lang.error_loading}",
                activityHistory_view_details_btn: "{$_lang.activityHistory_view_details_btn}",
                activityHistory_hide_details_btn: "{$_lang.activityHistory_hide_details_btn}",
                client_info: "{$_lang.client_info}",
                client_id: "{$_lang.client_id}",
                invoice_id: "{$_lang.invoice_id}",
                log_details: "{$_lang.log_details}",
                server_error: "{$_lang.server_communication_error|default:'Server Error'}"
            }
        };
    </script>

    <!-- Header -->
    <div class="gi-admin-header">
        <div class="gi-header-content">
            <div class="gi-logo">
                <i class="fas fa-file-invoice"></i>
                <h2>{$_ADDONLANG.guestinvoicemodule}</h2>
            </div>

            <nav class="gi-main-nav">
                <ul>
                    <li data-view="dashboard">
                        <a href="#dashboard"><i class="fas fa-home"></i> {$_ADDONLANG.nav_dashboard}</a>
                    </li>
                    <li data-view="settings">
                        <a href="#settings"><i class="fas fa-cog"></i> {$_ADDONLANG.nav_settings}</a>
                    </li>
                    <li data-view="activityHistory">
                        <a href="#activityHistory"><i class="fas fa-list"></i> {$_ADDONLANG.nav_activity_history}</a>
                    </li>
                    {* <li data-view="generate">
                        <a href="#generate"><i class="fas fa-link"></i> {$_ADDONLANG.nav_generate}</a>
                    </li> *}
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="gi-admin-container" id="app-container">
        <!-- Las vistas se cargarán aquí dinámicamente -->
        <div class="loading-spinner" id="loading-spinner">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Footer -->
    <div class="gi-admin-footer">
        <div class="gi-footer-content">
            <p>Guest Invoice Module v{$moduleVersion} &copy; {date('Y')}</p>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<!-- JavaScript -->
<!-- Then load our scripts -->
<script type="text/javascript" src="{$assetPath}js/activityHistory.js"></script>
{* <script type="text/javascript" src="{$assetPath}js/admin.js"></script> *}
<script type="text/javascript" src="{$assetPath}js/app.js"></script>
<!-- Initialize components after all scripts are loaded -->
<style>
/* Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    margin: 20px 0;
    list-style: none;
    padding: 0;
}

.pagination li {
    margin: 0 3px;
}

.pagination a, .pagination span {
    display: inline-block;
    padding: 6px 12px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #0f6ecd;
    text-decoration: none;
    border-radius: 3px;
    transition: all 0.2s;
}

.pagination a:hover {
    background: #e9ecef;
}

.pagination .active a, .pagination .active span {
    background: #0f6ecd;
    color: white;
    border-color: #0f6ecd;
    cursor: default;
}

.pagination .disabled a, .pagination .disabled span {
    color: #6c757d;
    pointer-events: none;
    background: #f8f9fa;
    border-color: #dee2e6;
}

/* Table styles */
.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid #dee2e6;
}

th, td {
    padding: 12px;
    text-align: left;
}

th {
    background-color: #f8f9fa;
    font-weight: 600;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}
</style>

<!-- JavaScript Includes -->
{$js_includes}

</main>
