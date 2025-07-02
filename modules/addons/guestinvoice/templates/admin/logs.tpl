<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{$_ADDONLANG.logs_title}</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{$_ADDONLANG.logs_id}</th>
                    <th>{$_ADDONLANG.logs_action}</th>
                    <th>{$_ADDONLANG.logs_date}</th>
                    <th>{$_ADDONLANG.logs_details}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$logs item=log}
                <tr>
                    <td>{$log->id}</td>
                    <td>{$log->action|escape}</td>
                    <td>{$log->datetime}</td>
                    <td>
                        <button class="btn btn-xs btn-info" onclick="showLogDetails({$log->id})">
                            {$_ADDONLANG.logs_view_details_btn}
                        </button>
                        <div id="log-details-{$log->id}" style="display:none;">
                            <strong>{$_ADDONLANG.logs_request}</strong><br>
                            <pre>{$log->request|json_decode:true|var_dump}</pre>
                            <strong>{$_ADDONLANG.logs_response}</strong><br>
                            <pre>{$log->response|json_decode:true|var_dump}</pre>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        
        {if $totalPages > 1}
        <nav>
            <ul class="pagination">
                {for $i=1 to $totalPages}
                <li class="{if $i == $currentPage}active{/if}">
                    <a href="{$modulelink}&action=logs&page={$i}">{$i}</a>
                </li>
                {/for}
            </ul>
        </nav>
        {/if}
    </div>
</div>

<script>
function showLogDetails(id) {
    var details = document.getElementById('log-details-' + id);
    if (details.style.display === 'none') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}
</script>