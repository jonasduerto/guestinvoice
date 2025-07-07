{include file="email/email_header.tpl"}

<p>Hello {$client_name},</p>

<p>A payment link has been generated for your invoice #{$invoice_num}.</p>

<p>You can access your invoice by clicking the following link:</p>

<p style="text-align: center; margin: 20px 0;">
    <a href="{$guest_link}" style="background-color: #0088cc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">View and Pay Invoice #{$invoice_num}</a>
</p>

<p>Or copy and paste this URL into your browser:<br>
<code>{$guest_link}</code></p>

<p>This link will expire on: <strong>{$expiry_date}</strong></p>

<p>If you did not request this link, please ignore this message.</p>

{include file="email/email_footer.tpl"}
