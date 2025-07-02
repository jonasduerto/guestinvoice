{include file=$tplVar.header}



<div class="guestInvoiceModule" style="padding: 20px;">

    <div class="col-md-12 error_message">

        {if $tplVar['errorMsg']}

            <div class="alert alert-danger" role="alert">

                {foreach $tplVar['errorMsg'] as $msg}

                    <p>{$msg}</p>

                {/foreach}

            </div>

        {else if $tplVar['successMsg']}
            <div class="successbox"><strong><span class="title">Success</span></strong><br>{$tplVar['successMsg']}</div>

        {/if}

    </div>
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page" style="font-size: 17px;"><i
                            class="fas fa-cogs" aria-hidden="true"></i> {$tplVar._lang.admin.settings}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-12" style="margin-top: 10px;">
            <form method="POST" name="guestinvoice" id="guestInvoiceForm">
                <table class="form" width="100%" cellspacing="2" cellpadding="3" border="0">
                    <tbody>

                        <tr>
                            <td class="fieldlabel" width="25%">
                                {$tplVar._lang.admin.show_invoice_btn}
                            </td>
                            <td class="fieldarea">
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="viewInvoice" name="viewInvoiceBtn"
                                        {($tplVar['viewInvoiceBtn']=='enabled' )? 'checked' : '' }>
                                    {$tplVar._lang.admin.show_invoice_btn_desc}
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="25%">
                                {$tplVar._lang.admin.Showinvoicevisitcount}
                            </td>
                            <td class="fieldarea">
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="view_on_adminside" name="view_on_adminside"
                                        {($tplVar['view_on_adminside']=='enabled' )? 'checked' : '' }>
                                    {$tplVar._lang.admin.Showinvoicevisitcount_desc}
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel">{$tplVar._lang.admin.invoice_email_templates}</td>
                            <td class="fieldarea">
                                {assign var="invoiceTemplateArr" value=","|explode:$tplVar['invoice_template']}
                                <select name="invoiceTemplate[]" id="invoiceTemplate"
                                    class="form-control custom-select select-inline" multiple="multiple">
                                    <option value="Invoice Payment Confirmation"
                                        {if in_array('Invoice Payment Confirmation', $invoiceTemplateArr)}selected{/if}>
                                        {$tplVar._lang.admin.invoice_payment_confirmation}</option>
                                    <option value="Invoice Payment Reminder"
                                        {if in_array('Invoice Payment Reminder', $invoiceTemplateArr)}selected{/if}>
                                        {$tplVar._lang.admin.invoice_payment_reminder}</option>
                                    <option value="Invoice Created"
                                        {if in_array('Invoice Created', $invoiceTemplateArr)}selected{/if}>
                                        {$tplVar._lang.admin.invoice_created}</option>
                                    <option value="First Invoice Overdue Notice"
                                        {if in_array('First Invoice Overdue Notice', $invoiceTemplateArr)}selected{/if}>
                                        {$tplVar._lang.admin.first_invoice_overdue_notice}</option>
                                    <option value="Second Invoice Overdue Notice"
                                        {if in_array('Second Invoice Overdue Notice', $invoiceTemplateArr)}selected{/if}>
                                        {$tplVar._lang.admin.second_invoice_overdue_notice}</option>
                                    <option value="Third Invoice Overdue Notice"
                                    {if in_array('Third Invoice Overdue Notice', $invoiceTemplateArr)}selected{/if}>
                                    {$tplVar._lang.admin.third_invoice_overdue_notice}</option>
                                    <option value="Credit Card Invoice Created"
                                    {if in_array('Credit Card Invoice Created', $invoiceTemplateArr)}selected{/if}>
                                    {$tplVar._lang.admin.credit_card_invoice_created}</option>
                                </select>
                                <span>{$tplVar._lang.admin.select_invoice_template_to_send_mail}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="25%">
                                {$tplVar._lang.admin.sendinvoicelink}
                            </td>
                            <td class="fieldarea">
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="invoice_link" name="invoice_link"
                                        {($tplVar['invoice_link']=='enabled' )? 'checked' : '' }>
                                    {$tplVar._lang.admin.sendinvoicelink_desc}
                                </label>
                                <div class="alert alert-info top-margin-5 bottom-margin-5 emailtemplinks">
                                    {$tplVar._lang.admin.sendinvoicelink_note}
                                    <br /><strong>{$tplVar._lang.admin.email_templates}:</strong>
                                    <a href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['Invoice Created']}"
                                        target="_blank"> {$tplVar._lang.admin.invoice_created}</a>,<a
                                        href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['Invoice Payment Confirmation']}"
                                        target="_blank"> {$tplVar._lang.admin.invoice_payment_confirmation}</a>,<a
                                        href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['Invoice Payment Reminder']}"
                                        target="_blank"> {$tplVar._lang.admin.invoice_payment_reminder}</a>,<a
                                        href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['First Invoice Overdue Notice']}"
                                        target="_blank"> {$tplVar._lang.admin.first_invoice_overdue_notice}</a>,<a
                                        href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['Second Invoice Overdue Notice']}"
                                        target="_blank"> {$tplVar._lang.admin.second_invoice_overdue_notice} </a>,<a
                                        href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['Third Invoice Overdue Notice']}"
                                        target="_blank"> {$tplVar._lang.admin.third_invoice_overdue_notice} </a>,<a
                                        href="configemailtemplates.php?action=edit&id={$tplVar.invoiceTemplateTempId['Credit Card Invoice Created']}"
                                        target="_blank"> {$tplVar._lang.admin.credit_card_invoice_created} </a>
                                    {$tplVar._lang.admin.email_templates}.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="25%">
                                {$tplVar._lang.admin.expire}
                            </td>
                            <td class="fieldarea">
                                <input type="number" name="invoice_link_validity" id="invoice_link_validity"
                                    pattern="[0-9]+" value="{$tplVar['invoice_link_validity']}"
                                    class="form-control input-80 input-inline">
                                {$tplVar._lang.admin.invoice_link_validity_desc}
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="25%">
                            {$tplVar._lang.admin.showguestinvoicebutton}
                            </td>
                            <td class="fieldarea">
                            <input type="checkbox" id="guest_view_invoice_btn" name="guest_view_invoice_btn" {($tplVar['viewInvoiceBtnEnable']=='enabled' )? 'checked' : '' }>
                            {$tplVar._lang.admin.showguestinvoicebuttondesc}
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="25%">
                            {$tplVar._lang.admin.show_recaptcha}
                            </td>
                            <td class="fieldarea">
                            <input type="checkbox" id="guest_invoice_recaptcha" name="guest_invoice_recaptcha" {($tplVar['recaptchaEnable']=='enabled' )? 'checked' : '' }>
                            {$tplVar._lang.admin.enabletoshowguestinfo}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="btn-container">
                    <input type="submit" value="Save Changes" name="updateInvoiceConfig" class="btn btn-dark"
                        style="background:#1a4d80;color:white;">
                    <input type="reset" value="Cancel Changes" name="updateInvoiceReset" class="btn btn-default">
                </div>

            </form>
        </div>
    </div>
</div>
{include file=$tplVar.footer}
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.10/clipboard.min.js"></script>
<script>
    $('#invoiceTemplate').select2();

    function copyToClipboard(obj) {
        let data = $(obj).data("clipboard-text");
        $('#copybtn').tooltip({
            trigger: 'click',
            placement: 'bottom'
        });

        function setTooltip(btn, message) {
            $(btn).tooltip('hide').attr('data-original-title', message).tooltip('show');
        }

        function hideTooltip(btn) {
            setTimeout(function() {
                $(btn).tooltip('hide');
            }, 1000);
        }
        var clipboard = new Clipboard('#copybtn');
        clipboard.on('success', function(e) {
            setTooltip(e.trigger, 'Copied!');
            hideTooltip(e.trigger);
        });
    }
</script>