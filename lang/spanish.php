<?php
if (!defined("WHMCS")) die();

$_ADDONLANG = [
    'generate_link' => 'Generate Guest Link',
    'generate_temporary_access_link' => 'Generate Temporary Access Link',
    'access_duration' => 'Access Duration',
    'one_hour' => '1 Hour',
    'twenty_four_hours' => '24 Hours',
    'three_days' => '3 Days',
    'seven_days' => '7 Days',
    'custom_duration' => 'Custom Duration...',
    'enter_hours' => 'Enter hours (1-720)',
    'send_link_to_client_via_email' => 'Send link to client via email',
    'cancel' => 'Cancel',
    'generating' => 'Generating...',
    'please_enter_valid_duration' => 'Please enter a valid duration between 1-720 hours',
    'link_generated' => 'Link Generated',
    'expires' => 'Expires',
    'email_sent_to_client' => 'Email sent to client',
    'copied' => 'Copied!',
    'failed_to_generate_link' => 'Failed to generate link',
    'server_communication_error' => 'Server communication error',

    // Variables de idioma generales
    'linkExpiredError' => 'Link has been expired.<br>Contact with account owner or support to get new link.',
    'requiredPermissionstoaccess' => 'You do not have the required permissions to access this page',
    'contactMasterAccountOwner' => 'Contact the master account owner if you feel this to be an error.',
    'Oops' => 'Oops!',
    'guestInvoiceErrorPagetitle' => 'Guest Invoice Error',
    'guestInvoiceErrorBreadCrumb' => 'Guest Invoice Error',
    'gst_inv_info' => 'Guest Invoice Information',
    'generate_manual_link' => 'Generate Manual Link',
    'generate_sub_acc' => 'Create Sub Account',
    'an_err_occr' => 'An Error Occurred',
    'link_crtd_success' => 'Guest invoice link has been successfully generated',
    'copy_gst_link' => 'Copy Guest Invoice Link',
    'gst_inv_add_usr' => 'Add User',
    'gst_inv_valid_upto' => 'Valid Upto',
    'gst_inv_2hr' => '2 Hours',
    'gst_inv_5hr' => '5 Hours',
    'gst_inv_10hr' => '10 Hours',
    'gst_inv_1day' => '1 Day',
    'gst_inv_2days' => '2 Days',
    'gst_inv_7days' => '7 Days',
    'gst_inv_addusr_desc' => 'Enter the bellow guest information to get guest payment link',
    'gst_inv_addusr_fname' => 'First Name',
    'gst_inv_addusr_lname' => 'Last Name',
    'gst_inv_addusr_email' => 'Email',
    'gst_inv_genlink' => 'Generate Link',
    'gst_inv_getlink' => 'Get Guest Invoice Link',
    'gst_inv_close' => 'Close',
    'gst_inv_fname_req' => 'The first name field is required',
    'gst_inv_lname_req' => 'The last name field is required',
    'gst_inv_email_inc' => 'The email address is incorrect',
    'gst_inv_email_req' => "The email address is required",
    'gst_inv_pw_weak' => "The password is Weak (should be atleast 8 characters.)",
    'gst_inv_pw_req' => "The password field is required",
    'gst_inv_verify_recaptcha' => "Please verify reCAPTCHA",
    'gst_inv_wait' => "Please Wait...",
    'gst_inv_copy_url' => "Copy Guest Invoice Link",
    'gst_inv_uname_req' => "The User name field is required.",

    // Dashboard
    'dashboard_active_links' => "Active Links",
    'dashboard_active_links_desc' => "Active temporary links that have not yet expired.",
    'dashboard_today_access' => "Today\'s Access",
    'dashboard_today_access_desc' => "Number of times the links have been accessed today.",
    'dashboard_expired_links' => "Expired Links",
    'dashboard_expired_links_desc' => "Links that have reached their expiration date.",
    'dashboard_quick_actions' => "Quick Actions",
    'dashboard_settings_btn' => "Settings",
    'dashboard_logs_btn' => "View Logs",
    'dashboard_cleanup_btn' => "Clean Expired Links",
    'dashboard_cleanup_confirm' => "Are you sure you want to mark all expired links as expired?",

    // Settings
    'settings_success' => "Settings saved successfully.",
    'settings_title' => "Module Settings",
    'settings_show_button_label' => "Show button on invoices",
    'settings_enabled' => "Enabled",
    'settings_disabled' => "Disabled",
    'settings_link_validity_label' => "Link validity (hours)",
    'settings_link_validity_desc' => "1-168 hours (1 week maximum)",
    'settings_recaptcha_label' => "Show reCAPTCHA",
    'settings_logging_label' => "Enable logs",
    'settings_save_btn' => "Save Settings",

    // Guest Links Table
    'guest_links' => 'Enlaces de Factura de Invitado',
    'invoice_id' => 'ID de Factura',
    'client' => 'Cliente',
    'company' => 'Empresa',
    'created' => 'Creado',
    'expires' => 'Expira',
    'status' => 'Estado',
    'access_count' => 'Accesos',
    'actions' => 'Acciones',
    'active' => 'Activo',
    'expired' => 'Expirado',
    'copy_link' => 'Copiar Enlace',
    'copied' => '¡Copiado!',
    'view_invoice' => 'Ver Factura',
    'no_links_found' => 'No se encontraron enlaces de factura de invitado.',

    // Logs
    'activityHistory_title' => "Registros del Sistema",
    'activityHistory_id' => "ID",
    'activityHistory_action' => "Action",
    'activityHistory_date' => "Date",
    'activityHistory_details' => "Details",
    'activityHistory_view_details_btn' => "View Details",
    'activityHistory_hide_details_btn' => "Hide Details",
    'activityHistory_request' => "Request",
    'activityHistory_response' => "Response",

    // Logs Page
    'activityHistory_id' => "ID",
    'activityHistory_action' => "Action",
    'activityHistory_date' => "Date",
    'activityHistory_details' => "Details",
    'activityHistory_view_details_btn' => "View Details",
    'activityHistory_hide_details_btn' => "Hide Details",
    'activityHistory_request' => "Request",
    'activityHistory_response' => "Response",
    'activityHistory_client' => "Cliente",
    'activityHistory_invoice' => "Factura",
    'activityHistory_client_id' => "ID del Cliente",
    'clear_logs' => 'Limpiar Registros',
    'clear_counters' => 'Reiniciar Contadores',
    'clearing' => 'Limpiando...',
    'logs_cleared' => 'Todos los registros de actividad han sido eliminados correctamente.',
    'counters_cleared' => 'Los contadores de acceso se han reiniciado correctamente.',
    'error_clearing_logs' => 'Ocurrió un error al intentar limpiar los registros. Por favor, inténtelo de nuevo.',
    'error_clearing_counters' => 'Error al reiniciar los contadores de acceso. Por favor, inténtelo de nuevo.',
    'confirm_clear_logs_message' => '¿Está seguro de que desea borrar todos los registros de actividad? Esta acción no se puede deshacer.',
    'confirm_clear_counters_message' => '¿Está seguro de que desea reiniciar todos los contadores de acceso a cero? Esta acción no se puede deshacer.',
    'activityHistory_invoice_id' => "Invoice ID",
    'client_info' => "Client Information",
    'invoice_info' => "Invoice Information",
    'export' => "Export",
    'log_details' => "Log Details",

    // Hooks
    'generate_temp_link_btn' => "Generate Temporary Link",
    'ajax_access_denied' => "Access Denied",
    'ajax_unauthenticated' => "Unauthenticated user",
    'ajax_invalid_invoice_id' => "Invalid invoice ID",
    'ajax_invalid_duration' => "Invalid duration",
    'ajax_invoice_not_found' => "Invoice not found",
    'ajax_link_generation_failed' => "Could not generate link",
    'ajax_internal_server_error' => "Internal server error",
    'email_subject_prefix' => "Temporary link for invoice #",
    'email_title' => "Temporary link for invoice",
    'email_greeting' => "Hello",
    'email_body_1' => "A temporary link has been generated to access your invoice",
    'email_link_label' => "Link:",
    'email_valid_for_label' => "Valid for:",
    'email_hours_label' => "hours",
    'email_expires_label' => "Expires:",
    'email_footer_1' => "This link is temporary and will expire automatically.",
    'email_footer_2' => "Regards,",
    'email_footer_3' => "The support team",

    // Navigation
    'nav_dashboard' => 'Dashboard',
    'nav_settings'  => 'Settings',
    'nav_activityHistory'      => 'Activity History',
    'nav_generate'  => 'Generate Link',

    // Header and Navigation
    'nav_dashboard' => "Dashboard",
    'nav_settings' => "Settings",
    'nav_activityHistory' => "Activity History",
    'nav_generate' => "Generate Link",

    // Page Titles
    'guestinvoicemodule' => "Guest Invoice System",
    'guestinvoicedesc' => "Generate temporary links to share invoices without login",
    'guestinvoiceinfo' => "Configure default link duration and notification preferences",
    'activityHistory_title' => "Activity Logs",

    // Common Terms
    'savechanges' => "Save Changes",
    'statistics' => "Statistics",

    // Settings Page
    'defaultduration' => "Default Duration",
    'hour' => "hour",
    'hours' => "hours",
    'sendemailbydefault' => "Send email automatically when generating link",
    'notificationemail' => "Notification Email",

    // Stats
    'totallinksgenerated' => "Total Links Generated",
    'activelinks' => "Active Links",
    'expiredlinks' => "Expired Links",
    'totalaccesses' => "Total Accesses",

    // Dashboard Page
    'quick_actions' => "Quick Actions",
    'system_config' => "System Configuration",
    'activity_logs_desc' => "View and manage system activity logs",
    'activity_history' => "Activity History",
    'clean_system' => "Clean System",
    'system_title' => "Guest Invoice System",

    // Modal
    'modal_close' => "Close",

    // Error Template
    'error_title' => 'Error',
    'error_back' => 'Go Back',
    'error_message' => 'Error Message',

    // Generate Page
    'generate_title' => 'Guest Invoice System',
    'generate_description' => 'Generate temporary links to share invoices without login',
    'quick_actions' => 'Quick Actions',
    'system_settings' => 'System Settings',
    'activity_logs' => 'Activity Logs',
    'export' => 'Export',
    'log_details' => 'Log Details',
    'request' => 'Request',
    'response' => 'Response',
    'close' => 'Close',
    'view_details' => 'View Details',
    'id' => 'ID',
    'action' => 'Action',
    'date' => 'Date',
    'details' => 'Details',
    'link_generated' => 'Link Generated',
    'invoice_accessed' => 'Invoice Accessed',
    'link_expired' => 'Link Expired',
    'default_duration_config' => 'Configure default link duration and notification preferences',
    'next' => 'Next'
];
?>