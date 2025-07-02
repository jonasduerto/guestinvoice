<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Guest Invoice Module
 * 
 * Permite generar enlaces temporales para acceder a facturas sin necesidad de login
 */

function guestinvoice_config() {
    return [
        'name' => 'Guest Invoice',
        'description' => 'Temporary invoice links for guests.','version' => '1.0.0',
        'author' => 'Default',
        'language' => 'english',
        'fields' => [
            'enable_invoice_btn' => [
                'FriendlyName' => 'Show guest invoice button',
                'Type' => 'yesno',
                'Default' => 'enabled',
                'Description' => 'Enable to show Guest invoice button on clientarea invoice page',
            ],
            'invoice_link_validity' => [
                'FriendlyName' => 'Link Validity (hours)',
                'Type' => 'text',
                'Size' => '5',
                'Default' => '24',
                'Description' => 'Expiration hour for guest invoice link. [Default is 24 hours]',
            ],
            'recaptchaEnable' => [
                'FriendlyName' => 'Show reCaptcha',
                'Type' => 'yesno',
                'Default' => 'disabled',
                'Description' => 'Enable to show reCaptcha on guest invoice form.',
            ],
        ]
    ];
}

function guestinvoice_activate() {
    try {
        // guest_invoice
        if (!Capsule::schema()->hasTable('guest_invoice')) {
            Capsule::schema()->create('guest_invoice', function ($table) {
                $table->increments('id');
                $table->integer('userId');
                $table->integer('clientId');
                $table->integer('invoiceId');
                $table->string('authid', 255);
                $table->integer('validtime');
                $table->longText('referralLink');
                $table->tinyInteger('status');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
        // guest_invoice_logs
        if (!Capsule::schema()->hasTable('guest_invoice_logs')) {
            Capsule::schema()->create('guest_invoice_logs', function ($table) {
                $table->increments('id');
                $table->timestamp('datetime')->useCurrent();
                $table->string('module', 255)->nullable();
                $table->text('action');
                $table->mediumText('request')->nullable();
                $table->mediumText('response')->nullable();
            });
        }
        // guest_invoice_setting
        if (!Capsule::schema()->hasTable('guest_invoice_setting')) {
            Capsule::schema()->create('guest_invoice_setting', function ($table) {
                $table->string('setting', 50);
                $table->string('value', 50);
            });
            Capsule::table('guest_invoice_setting')->insert([
                ['setting' => 'enable_invoice_btn', 'value' => 'enabled'],
                ['setting' => 'view_invoice_count', 'value' => 'enabled'],
                ['setting' => 'template', 'value' => 'Guest Invoice'],
                ['setting' => 'view_on_adminside', 'value' => 'enabled'],
                ['setting' => 'invoice_link', 'value' => 'enabled'],
                ['setting' => 'invoice_link_validity', 'value' => '24'],
                ['setting' => 'guest_invoice', 'value' => '1'],
                ['setting' => 'invoice_template', 'value' => 'Invoice Created'],
                ['setting' => 'recaptchaEnable', 'value' => 'disabled'],
                ['setting' => 'viewInvoiceBtnEnable', 'value' => 'enabled'],
            ]);
        }
        return [
            'status' => 'success',
            'description' => 'Guest Invoice tables created successfully.',
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Error: ' . $e->getMessage(),
        ];
    }
}

function guestinvoice_deactivate() {
    // Si quieres borrar las tablas al desactivar, descomenta:
    /*
    Capsule::schema()->dropIfExists('guest_invoice');
    Capsule::schema()->dropIfExists('guest_invoice_logs');
    Capsule::schema()->dropIfExists('guest_invoice_setting');
    */
    return [
        'status' => 'success',
        'description' => 'Guest Invoice module deactivated.',
    ];
}

function guestinvoice_output($vars) {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'dashboard';
    $dispatcher = new WHMCS\Module\Addon\GuestInvoice\Admin\AdminDispatcher();
    $response = $dispatcher->dispatch($action, $vars);
    echo $response;
}