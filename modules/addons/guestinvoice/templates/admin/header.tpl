<div style="border: 1px solid #cccccc;">

<link rel="stylesheet" type="text/css" href="{$tplVar.urlPath}css/style.css">


<div class="add_hdr">
    <a href="https://whmcsglobalservices.com/" class="small_logo" target="_blank"><img src="{$tplVar.urlPath}assets/img/wgs-logo.svg"></a>
    <div class="add_nav">
      <ul>
        <li>
          <a href="{$tplVar.moduleLink}" class="ad_home {if $tplVar.tab == 'license' }active {/if}"><i class="fa fa-home" aria-hidden="true"></i> {$tplVar._lang.admin.dashboard} </a>
        </li>
        {if $tplVar.license_status.status == 'Active'}
        <li class="">
          <a href="{$tplVar.moduleLink}&action=guestInvoice" class="ad_pr {if $tplVar.tab == 'guestInvoice' }active {/if}"><i class="fas fa-cogs" aria-hidden="true"></i> {$tplVar._lang.admin.settings}</a>
        </li>
        <li class="">
          <a href="{$tplVar.moduleLink}&action=logs" class="ad_pr {if $tplVar.tab == 'logs' } active {/if}"><i class="fad fa-copy"></i> {$tplVar._lang.admin.logs}</a>
        </li>  
        {/if}        
      </ul>
    </div>
</div>