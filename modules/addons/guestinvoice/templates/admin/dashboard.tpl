<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{$_ADDONLANG.dashboard_active_links}</h3>
            </div>
            <div class="panel-body">
                <h2>{$activeLinks}</h2>
                <p>{$_ADDONLANG.dashboard_active_links_desc}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{$_ADDONLANG.dashboard_today_access}</h3>
            </div>
            <div class="panel-body">
                <h2>{$todayAccess}</h2>
                <p>{$_ADDONLANG.dashboard_today_access_desc}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{$_ADDONLANG.dashboard_expired_links}</h3>
            </div>
            <div class="panel-body">
                <h2>{$expiredLinks}</h2>
                <p>{$_ADDONLANG.dashboard_expired_links_desc}</p>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{$_ADDONLANG.dashboard_quick_actions}</h3>
    </div>
    <div class="panel-body">
        <a href="{$modulelink}&action=settings" class="btn btn-primary">
            <i class="fas fa-cogs"></i> {$_ADDONLANG.dashboard_settings_btn}
        </a>
        <a href="{$modulelink}&action=logs" class="btn btn-info">
            <i class="fas fa-list"></i> {$_ADDONLANG.dashboard_logs_btn}
        </a>
        <a href="{$modulelink}&action=cleanup" class="btn btn-warning" onclick="return confirm('{$_ADDONLANG.dashboard_cleanup_confirm}');">
            <i class="fas fa-broom"></i> {$_ADDONLANG.dashboard_cleanup_btn}
        </a>
    </div>
</div>