
{include file=$tplVar.header}

<div id="wcn-client" class="row module-container">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page" style="font-size: 17px;">
                    <i class="fad fa-key"></i> {$_ADDONLANG['admin']['license']}
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{$_ADDONLANG['admin']['license_information']}</h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr>
                        <th>{$_ADDONLANG['admin']['version']}</th>
                        <td>V{$tplVar.version}</td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['registered_to']}</th>
                        <td>{$tplVar.registeredname}</td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['registered_email']}</th>
                        <td>{$tplVar.email}</td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['valid_domain']}</th>
                        <td>{$tplVar.explodedomain}</td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['license_key']}</th>
                        <td>{$tplVar.license_key}</td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['status']}</th>
                        <td><span class="label label-{if $tplVar.status|lower == 'active'}success{else}danger{/if}">{$tplVar.status}</span></td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['product_name']}</th>
                        <td>{$tplVar.productname}</td>
                    </tr>
                    <tr>
                        <th>{$_ADDONLANG['admin']['last_updated']}</th>
                        <td>{$smarty.now|date_format:"%d %B, %Y"}</td>
                    </tr>
                </table>
                
                <div class="text-center">
                    <a href="https://whmcsglobalservices.com/support" target="_blank" class="btn btn-primary">
                        <i class="fas fa-question-circle"></i> {$_ADDONLANG['admin']['support']}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{include file=$tplVar.footer}