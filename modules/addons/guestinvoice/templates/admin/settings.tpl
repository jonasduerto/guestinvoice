{if $success}
<div class="alert alert-success">{$_ADDONLANG.settings_success}</div>
{/if}

<form method="post" action="{$modulelink}&action=settings">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{$_ADDONLANG.settings_title}</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{$_ADDONLANG.settings_show_button_label}</label>
                <select name="show_invoice_button" class="form-control">
                    <option value="enabled" {if $settings.show_invoice_button == 'enabled'}selected{/if}>{$_ADDONLANG.settings_enabled}</option>
                    <option value="disabled" {if $settings.show_invoice_button == 'disabled'}selected{/if}>{$_ADDONLANG.settings_disabled}</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>{$_ADDONLANG.settings_link_validity_label}</label>
                <input type="number" name="link_validity_hours" value="{$settings.link_validity_hours}" class="form-control" min="1" max="168">
                <small class="form-text text-muted">{$_ADDONLANG.settings_link_validity_desc}</small>
            </div>
            
            <div class="form-group">
                <label>{$_ADDONLANG.settings_recaptcha_label}</label>
                <select name="show_recaptcha" class="form-control">
                    <option value="enabled" {if $settings.show_recaptcha == 'enabled'}selected{/if}>{$_ADDONLANG.settings_enabled}</option>
                    <option value="disabled" {if $settings.show_recaptcha == 'disabled'}selected{/if}>{$_ADDONLANG.settings_disabled}</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>{$_ADDONLANG.settings_logging_label}</label>
                <select name="module_logging" class="form-control">
                    <option value="1" {if $settings.module_logging == '1'}selected{/if}>{$_ADDONLANG.settings_enabled}</option>
                    <option value="0" {if $settings.module_logging == '0'}selected{/if}>{$_ADDONLANG.settings_disabled}</option>
                </select>
            </div>
            
            <button type="submit" name="save" value="true" class="btn btn-primary">{$_ADDONLANG.settings_save_btn}</button>
        </div>
    </div>
</form>