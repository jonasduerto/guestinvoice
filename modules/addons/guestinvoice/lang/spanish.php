<?php
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

$_ADDONLANG['linkExpiredError'] = 'El enlace ha expirado.<br>Contacte con el propietario de la cuenta o con el soporte para obtener un nuevo enlace.';
$_ADDONLANG['requiredPermissionstoaccess'] = 'No tiene los permisos requeridos para acceder a esta página';
$_ADDONLANG['contactMasterAccountOwner'] = 'Contacte al propietario de la cuenta maestra si considera que esto es un error.';
$_ADDONLANG['Oops'] = '¡Ups!';
$_ADDONLANG['guestInvoiceErrorPagetitle'] = 'Error en la Factura de Invitado';
$_ADDONLANG['guestInvoiceErrorBreadCrumb'] = 'Error en la Factura de Invitado';
$_ADDONLANG['gst_inv_info'] = "Información de la Factura de Invitado";
$_ADDONLANG['generate_manual_link'] = "Generar Enlace Manual";
$_ADDONLANG['generate_sub_acc'] = "Crear Subcuenta";
$_ADDONLANG['an_err_occr'] = "Ocurrió un Error";
$_ADDONLANG['link_crtd_success'] = "El enlace de la factura de invitado se ha generado exitosamente";
$_ADDONLANG['copy_gst_link'] = "Copiar Enlace de la Factura de Invitado";
$_ADDONLANG['gst_inv_add_usr'] = "Agregar Usuario";
$_ADDONLANG['gst_inv_valid_upto'] = "Válido Hasta";
$_ADDONLANG['gst_inv_2hr'] = "2 Horas";
$_ADDONLANG['gst_inv_5hr'] = "5 Horas";
$_ADDONLANG['gst_inv_10hr'] = "10 Horas";
$_ADDONLANG['gst_inv_1day'] = "1 Día";
$_ADDONLANG['gst_inv_2days'] = "2 Días";
$_ADDONLANG['gst_inv_7days'] = "7 Días";
$_ADDONLANG['gst_inv_addusr_desc'] = "Ingrese la información del invitado a continuación para obtener el enlace de pago del invitado";
$_ADDONLANG['gst_inv_addusr_fname'] = "Nombre";
$_ADDONLANG['gst_inv_addusr_lname'] = "Apellido";
$_ADDONLANG['gst_inv_addusr_email'] = "Correo Electrónico";
$_ADDONLANG['gst_inv_genlink'] = "Generar Enlace";
$_ADDONLANG['gst_inv_getlink'] = "Obtener Enlace de la Factura de Invitado";
$_ADDONLANG['gst_inv_close'] = "Cerrar";
$_ADDONLANG['gst_inv_fname_req'] = "El campo del nombre es obligatorio";
$_ADDONLANG['gst_inv_lname_req'] = "El campo del apellido es obligatorio";
$_ADDONLANG['gst_inv_email_inc'] = "La dirección de correo electrónico es incorrecta";
$_ADDONLANG['gst_inv_email_req'] = "El correo electrónico es obligatorio";
$_ADDONLANG['gst_inv_pw_weak'] = "La contraseña es débil (debe tener al menos 8 caracteres.)";
$_ADDONLANG['gst_inv_pw_req'] = "El campo de la contraseña es obligatorio";
$_ADDONLANG['gst_inv_verify_recaptcha'] = "Por favor, verifique reCAPTCHA";
$_ADDONLANG['gst_inv_wait'] = "Por favor, espere...";
$_ADDONLANG['gst_inv_copy_url'] = "Copiar Enlace de la Factura de Invitado";
$_ADDONLANG['gst_inv_uname_req'] = "El campo del nombre de usuario es obligatorio.";

// Dashboard
$_ADDONLANG['dashboard_active_links'] = "Enlaces Activos";
$_ADDONLANG['dashboard_active_links_desc'] = "Enlaces temporales activos que aún no han expirado.";
$_ADDONLANG['dashboard_today_access'] = "Accesos de Hoy";
$_ADDONLANG['dashboard_today_access_desc'] = "Número de veces que se ha accedido a los enlaces hoy.";
$_ADDONLANG['dashboard_expired_links'] = "Enlaces Expirados";
$_ADDONLANG['dashboard_expired_links_desc'] = "Enlaces que han alcanzado su fecha de vencimiento.";
$_ADDONLANG['dashboard_quick_actions'] = "Acciones Rápidas";
$_ADDONLANG['dashboard_settings_btn'] = "Configuración";
$_ADDONLANG['dashboard_logs_btn'] = "Ver Logs";
$_ADDONLANG['dashboard_cleanup_btn'] = "Limpiar Enlaces Expirados";
$_ADDONLANG['dashboard_cleanup_confirm'] = "¿Estás seguro de que quieres marcar todos los enlaces caducados como expirados?";

// Settings
$_ADDONLANG['settings_success'] = "Configuración guardada correctamente.";
$_ADDONLANG['settings_title'] = "Configuración del Módulo";
$_ADDONLANG['settings_show_button_label'] = "Mostrar botón en facturas";
$_ADDONLANG['settings_enabled'] = "Habilitado";
$_ADDONLANG['settings_disabled'] = "Deshabilitado";
$_ADDONLANG['settings_link_validity_label'] = "Validez del enlace (horas)";
$_ADDONLANG['settings_link_validity_desc'] = "1-168 horas (1 semana máximo)";
$_ADDONLANG['settings_recaptcha_label'] = "Mostrar reCAPTCHA";
$_ADDONLANG['settings_logging_label'] = "Activar logs";
$_ADDONLANG['settings_save_btn'] = "Guardar Configuración";

// Logs
$_ADDONLANG['logs_title'] = "Logs del Sistema";
$_ADDONLANG['logs_id'] = "ID";
$_ADDONLANG['logs_action'] = "Acción";
$_ADDONLANG['logs_date'] = "Fecha";
$_ADDONLANG['logs_details'] = "Detalles";
$_ADDONLANG['logs_view_details_btn'] = "Ver Detalles";
$_ADDONLANG['logs_request'] = "Petición";
$_ADDONLANG['logs_response'] = "Respuesta";

// Hooks
$_ADDONLANG['generate_temp_link_btn'] = "Generar Enlace Temporal";
$_ADDONLANG['ajax_access_denied'] = "Acceso denegado";
$_ADDONLANG['ajax_unauthenticated'] = "Usuario no autenticado";
$_ADDONLANG['ajax_invalid_invoice_id'] = "ID de factura inválido";
$_ADDONLANG['ajax_invalid_duration'] = "Duración inválida";
$_ADDONLANG['ajax_invoice_not_found'] = "Factura no encontrada";
$_ADDONLANG['ajax_link_generation_failed'] = "No se pudo generar el enlace";
$_ADDONLANG['ajax_internal_server_error'] = "Error interno del servidor";
$_ADDONLANG['email_subject_prefix'] = "Enlace temporal para factura #";
$_ADDONLANG['email_title'] = "Enlace temporal para factura";
$_ADDONLANG['email_greeting'] = "Hola";
$_ADDONLANG['email_body_1'] = "Se ha generado un enlace temporal para acceder a tu factura";
$_ADDONLANG['email_link_label'] = "Enlace:";
$_ADDONLANG['email_valid_for_label'] = "Válido por:";
$_ADDONLANG['email_hours_label'] = "horas";
$_ADDONLANG['email_expires_label'] = "Expira:";
$_ADDONLANG['email_footer_1'] = "Este enlace es temporal y expirará automáticamente.";
$_ADDONLANG['email_footer_2'] = "Saludos,";
$_ADDONLANG['email_footer_3'] = "El equipo de soporte";