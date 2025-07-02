<?php
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

$_ADDONLANG['linkExpiredError'] = 'Link has been expired.<br>Contact with account owner or support to get new link.';
$_ADDONLANG['requiredPermissionstoaccess'] = 'You do not have the required permissions to access this page';
$_ADDONLANG['contactMasterAccountOwner'] = 'Contact the master account owner if you feel this to be an error.';
$_ADDONLANG['Oops'] = 'Oops!';
$_ADDONLANG['guestInvoiceErrorPagetitle'] = 'Guest Invoice Error';
$_ADDONLANG['guestInvoiceErrorBreadCrumb']  = 'Guest Invoice Error';
$_ADDONLANG['gst_inv_info'] = "Guest Invoice Informations";
$_ADDONLANG['generate_manual_link'] = "Generate Manual Link";
$_ADDONLANG['generate_sub_acc'] = "Create Sub Account";
$_ADDONLANG['an_err_occr'] = "An Error Occurred";
$_ADDONLANG['link_crtd_success'] = "Guest invoice link has been successfully generated";
$_ADDONLANG['copy_gst_link'] = "Copy Guest invoice Link";
$_ADDONLANG['gst_inv_add_usr'] = "Add User";
$_ADDONLANG['gst_inv_valid_upto'] = "Valid Upto";
$_ADDONLANG['gst_inv_2hr'] = "2 Hours";
$_ADDONLANG['gst_inv_5hr'] = "5 Hours";
$_ADDONLANG['gst_inv_10hr'] = "10 Hours";
$_ADDONLANG['gst_inv_1day'] = "1 Day";
$_ADDONLANG['gst_inv_2days'] = "2 Days";
$_ADDONLANG['gst_inv_7days'] = "7 Days";
$_ADDONLANG['gst_inv_addusr_desc'] = "Enter the bellow guest information to get guest payment link";
$_ADDONLANG['gst_inv_addusr_fname'] = "First Name";
$_ADDONLANG['gst_inv_addusr_lname'] = "Last Name";
$_ADDONLANG['gst_inv_addusr_email'] = "Email";
$_ADDONLANG['gst_inv_genlink'] = "Generate Link";
$_ADDONLANG['gst_inv_getlink'] = "Get Guest Invoice Link";
$_ADDONLANG['gst_inv_close'] = "Close";
$_ADDONLANG['gst_inv_fname_req'] = "The first name field is required";
$_ADDONLANG['gst_inv_lname_req'] = "The last name field is required";
$_ADDONLANG['gst_inv_email_inc'] = "The email address is incorrect";
$_ADDONLANG['gst_inv_email_req'] = "The email address is required";
$_ADDONLANG['gst_inv_pw_weak'] = "The password is Weak (should be atleast 8 characters.)";
$_ADDONLANG['gst_inv_pw_req'] = "The password field is required";
$_ADDONLANG['gst_inv_verify_recaptcha'] = "Please verify reCAPTCHA";
$_ADDONLANG['gst_inv_wait'] = "Please Wait...";
$_ADDONLANG['gst_inv_copy_url'] = "Copy Guest Invoice Link";
$_ADDONLANG['gst_inv_uname_req'] = "The User name field is required.";

// Dashboard
$_ADDONLANG['dashboard_active_links'] = "Active Links";
$_ADDONLANG['dashboard_active_links_desc'] = "Active temporary links that have not yet expired.";
$_ADDONLANG['dashboard_today_access'] = "Today's Access";
$_ADDONLANG['dashboard_today_access_desc'] = "Number of times the links have been accessed today.";
$_ADDONLANG['dashboard_expired_links'] = "Expired Links";
$_ADDONLANG['dashboard_expired_links_desc'] = "Links that have reached their expiration date.";
$_ADDONLANG['dashboard_quick_actions'] = "Quick Actions";
$_ADDONLANG['dashboard_settings_btn'] = "Settings";
$_ADDONLANG['dashboard_logs_btn'] = "View Logs";
$_ADDONLANG['dashboard_cleanup_btn'] = "Clean Expired Links";
$_ADDONLANG['dashboard_cleanup_confirm'] = "Are you sure you want to mark all expired links as expired?";

// Settings
$_ADDONLANG['settings_success'] = "Settings saved successfully.";
$_ADDONLANG['settings_title'] = "Module Settings";
$_ADDONLANG['settings_show_button_label'] = "Show button on invoices";
$_ADDONLANG['settings_enabled'] = "Enabled";
$_ADDONLANG['settings_disabled'] = "Disabled";
$_ADDONLANG['settings_link_validity_label'] = "Link validity (hours)";
$_ADDONLANG['settings_link_validity_desc'] = "1-168 hours (1 week maximum)";
$_ADDONLANG['settings_recaptcha_label'] = "Show reCAPTCHA";
$_ADDONLANG['settings_logging_label'] = "Enable logs";
$_ADDONLANG['settings_save_btn'] = "Save Settings";

// Logs
$_ADDONLANG['logs_title'] = "System Logs";
$_ADDONLANG['logs_id'] = "ID";
$_ADDONLANG['logs_action'] = "Action";
$_ADDONLANG['logs_date'] = "Date";
$_ADDONLANG['logs_details'] = "Details";
$_ADDONLANG['logs_view_details_btn'] = "View Details";
$_ADDONLANG['logs_request'] = "Request";
$_ADDONLANG['logs_response'] = "Response";

// Hooks
$_ADDONLANG['generate_temp_link_btn'] = "Generate Temporary Link";
$_ADDONLANG['ajax_access_denied'] = "Access Denied";
$_ADDONLANG['ajax_unauthenticated'] = "Unauthenticated user";
$_ADDONLANG['ajax_invalid_invoice_id'] = "Invalid invoice ID";
$_ADDONLANG['ajax_invalid_duration'] = "Invalid duration";
$_ADDONLANG['ajax_invoice_not_found'] = "Invoice not found";
$_ADDONLANG['ajax_link_generation_failed'] = "Could not generate link";
$_ADDONLANG['ajax_internal_server_error'] = "Internal server error";
$_ADDONLANG['email_subject_prefix'] = "Temporary link for invoice #";
$_ADDONLANG['email_title'] = "Temporary link for invoice";
$_ADDONLANG['email_greeting'] = "Hello";
$_ADDONLANG['email_body_1'] = "A temporary link has been generated to access your invoice";
$_ADDONLANG['email_link_label'] = "Link:";
$_ADDONLANG['email_valid_for_label'] = "Valid for:";
$_ADDONLANG['email_hours_label'] = "hours";
$_ADDONLANG['email_expires_label'] = "Expires:";
$_ADDONLANG['email_footer_1'] = "This link is temporary and will expire automatically.";
$_ADDONLANG['email_footer_2'] = "Regards,";
$_ADDONLANG['email_footer_3'] = "The support team";