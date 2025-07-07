<div class="gi-admin-container">
    {* <div class="gi-page-header">
        <h1><i class="fas fa-cog"></i> {$_lang.nav_settings}</h1>
        <p>{$_lang.guestinvoicedesc}</p>
    </div> *}

    <div class="gi-card">
        <div class="gi-card-header">
            <h3><i class="fas fa-sliders-h"></i> {$_lang.system_config}</h3>
        </div>
        <div class="gi-card-body">
            <div class="gi-alert gi-alert-info">
                <i class="fas fa-info-circle"></i> {$_lang.guestinvoiceinfo}
            </div>
            <form id="gi-settings-form" method="post" action="{$modulelink}">
                <input type="hidden" name="csrf_token" value="{$csrfToken}">
                <input type="hidden" name="module" value="guestinvoice">
                <input type="hidden" name="action" value="save_settings">
                <div class="gi-form-group">
                    <label for="defaultDuration" class="gi-form-label">{$_lang.defaultduration}</label>
                    <select name="default_duration" id="defaultDuration" class="gi-form-control">
                        <option value="1" {if $defaultDuration == 1}selected{/if}>1 {$_lang.hour}</option>
                        <option value="4" {if $defaultDuration == 4}selected{/if}>4 {$_lang.hours}</option>
                        <option value="12" {if $defaultDuration == 12}selected{/if}>12 {$_lang.hours}</option>
                        <option value="24" {if $defaultDuration == 24}selected{/if}>24 {$_lang.hours}</option>
                    </select>
                </div>
                <div class="gi-form-group">
                    <div class="gi-checkbox">
                        <input type="checkbox" name="send_email_default" id="sendEmailDefault" {if $sendEmailDefault}checked{/if}>
                        <label for="sendEmailDefault">{$_lang.sendemailbydefault}</label>
                    </div>
                </div>
                <div class="gi-form-group">
                    <label for="notificationEmail" class="gi-form-label">{$_lang.notificationemail}</label>
                    <input type="email" name="notification_email" id="notificationEmail" class="gi-form-control" value="{$notificationEmail}">
                </div>
                <button 
                    type="submit" 
                    class="gi-btn gi-btn-primary"
                    data-ajax-endpoint="{$ajaxEndpoint}"
                    data-csrf_token="{$csrf_token}">
                    <i class="fas fa-save"></i> {$_lang.savechanges}
                </button>
            </form>
        </div>
    </div>
    <div class="gi-stats-grid">
        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-link"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$totalLinks}</h3>
                <p>{$_lang.totallinksgenerated}</p>
            </div>
        </div>
        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$activeLinks}</h3>
                <p>{$_lang.activelinks}</p>
            </div>
        </div>
        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$totalAccesses}</h3>
                <p>{$_lang.totalaccesses}</p>
            </div>
        </div>
    </div>
</div>
