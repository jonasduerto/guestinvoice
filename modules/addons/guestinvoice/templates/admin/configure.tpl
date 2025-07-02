<div class="guest-invoice-admin">
    <div class="header">
        <h2>{$LANG.guestinvoicemodule}</h2>
        <p>{$LANG.guestinvoicedesc}</p>
    </div>
    
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> {$LANG.guestinvoiceinfo}
    </div>
    
    <form method="post" action="">
        <input type="hidden" name="module" value="guestinvoice">
        <input type="hidden" name="action" value="save">
        
        <div class="form-group">
            <label for="defaultDuration">{$LANG.defaultduration}</label>
            <select name="default_duration" id="defaultDuration" class="form-control">
                <option value="1" {if $defaultDuration == 1}selected{/if}>1 {$LANG.hour}</option>
                <option value="4" {if $defaultDuration == 4}selected{/if}>4 {$LANG.hours}</option>
                <option value="12" {if $defaultDuration == 12}selected{/if}>12 {$LANG.hours}</option>
                <option value="24" {if $defaultDuration == 24}selected{/if}>24 {$LANG.hours}</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="sendEmailDefault">
                <input type="checkbox" name="send_email_default" id="sendEmailDefault" {if $sendEmailDefault}checked{/if}>
                {$LANG.sendemailbydefault}
            </label>
        </div>
        
        <div class="form-group">
            <label for="notificationEmail">{$LANG.notificationemail}</label>
            <input type="email" name="notification_email" id="notificationEmail" class="form-control" value="{$notificationEmail}">
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> {$LANG.savechanges}
        </button>
    </form>
    
    <hr>
    
    <div class="stats">
        <h3>{$LANG.statistics}</h3>
        
        <div class="row">
            <div class="col-md-4">
                <div class="stat-box">
                    <h4>{$totalLinks}</h4>
                    <p>{$LANG.totallinksgenerated}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h4>{$activeLinks}</h4>
                    <p>{$LANG.activelinks}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h4>{$totalAccesses}</h4>
                    <p>{$LANG.totalaccesses}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.guest-invoice-admin {
    max-width: 900px;
    margin: 0 auto;
}

.stat-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    text-align: center;
}

.stat-box h4 {
    font-size: 24px;
    margin: 0 0 5px 0;
}

.stat-box p {
    color: #6c757d;
    margin: 0;
}
</style>