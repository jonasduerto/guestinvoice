<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{$_LANG.invoicenumber}{$invoiceData.invoiceid}</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .invoice-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="d-flex justify-content-between">
            <h1>{$_LANG.invoicenumber}{$invoiceData.invoiceid}</h1>
            <img src="{$logo_url}" alt="Logo" height="50">
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <strong>{$_LANG.invoicesbillto}:</strong><br>
                {$clientData.firstname} {$clientData.lastname}<br>
                {$clientData.address1}<br>
                {if $clientData.address2}{$clientData.address2}<br>{/if}
                {$clientData.city}, {$clientData.state}, {$clientData.postcode}<br>
                {$clientData.countryname}
            </div>
            <div class="col-md-6 text-right">
                <strong>{$_LANG.invoicesdatecreated}:</strong> {$invoiceData.date}<br>
                <strong>{$_LANG.invoicesdatedue}:</strong> {$invoiceData.duedate}<br>
                <strong>{$_LANG.invoicestotal}:</strong> {$invoiceData.total}
            </div>
        </div>
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{$_LANG.invoicesdescription}</th>
                    <th>{$_LANG.invoicesqty}</th>
                    <th>{$_LANG.invoicesrate}</th>
                    <th>{$_LANG.invoicesamount}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$invoiceData.items.item item=item}
                <tr>
                    <td>{$item.description}</td>
                    <td>1</td>
                    <td>{$item.amount}</td>
                    <td>{$item.amount}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        <div class="text-right">
            <h4>{$_LANG.invoicessubtotal}: {$invoiceData.subtotal}</h4>
            <h4>{$_LANG.invoicestax}: {$invoiceData.tax}</h4>
            <h3>{$_LANG.invoicestotal}: {$invoiceData.total}</h3>
        </div>
        <hr>
        <div class="text-center">
            <a href="viewinvoice.php?id={$invoiceData.invoiceid}&pagetoken={$pagetoken}" class="btn btn-primary">{$_LANG.invoicespaynow}</a>
        </div>
    </div>
</body>
</html>