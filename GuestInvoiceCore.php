<?php
namespace GuestInvoice;

if (!defined('WHMCS') && !isset($_REQUEST['ajax_action'])) {
    die('This file cannot be accessed directly');
}

use Smarty;
use Exception;
use Carbon\Carbon;
use SmartyException;
use WHMCS\Config\Setting;
use WHMCS\Database\Capsule;
use GuestInvoice\Services\LinkService;
use GuestInvoice\Services\SecurityService;

class GuestInvoiceCore {
    protected static $instance;
    protected $moduleName = 'guestinvoice';
    protected $smarty;

    public function __construct() {
        // Constructor básico
    }

    public static function getInstance() {
        if (!static::$instance || !(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function config() {
        return [
            'name' => 'Guest Invoice',
            'description' => 'Enlaces temporales para pagos sin login',
            'version' => '2.0',
            'fields' => [
                'invoice_link_validity' => [
                    'FriendlyName' => 'Validez del enlace (horas)',
                    'Type' => 'text',
                    'Default' => '24',
                ],
                'viewInvoiceBtnEnable' => [
                    'FriendlyName' => 'Mostrar botón en facturas',
                    'Type' => 'yesno',
                    'Default' => 'yes'
                ],
                'recaptchaEnable' => [
                    'FriendlyName' => 'Habilitar reCAPTCHA',
                    'Type' => 'yesno',
                    'Default' => 'no'
                ]
            ]
        ];
    }

    public function activate() {
        try {
            // Tabla principal de enlaces
            if (!Capsule::schema()->hasTable('guest_invoice')) {
                Capsule::schema()->create('guest_invoice', function ($table) {
                    $table->increments('id');
                    $table->integer('userId');
                    $table->integer('clientId');
                    $table->integer('invoiceId');
                    $table->string('authid', 64);
                    $table->integer('validtime');
                    $table->text('referralLink');
                    $table->boolean('status')->default(true);
                    $table->integer('access_count')->default(0);
                    $table->timestamps();
                });
            }

            // Tabla de logs
            if (!Capsule::schema()->hasTable('guest_invoice_logs')) {
                Capsule::schema()->create('guest_invoice_logs', function ($table) {
                    $table->increments('id');
                    $table->string('module', 100);
                    $table->string('action', 100);
                    $table->integer('invoice_id');
                    $table->integer('client_id');
                    $table->timestamp('datetime')->useCurrent();
                    $table->text('data')->nullable();
                    $table->text('request')->nullable();
                    $table->text('response')->nullable();
                    $table->timestamps();
                });
            }

            // Tabla de configuración
            if (!Capsule::schema()->hasTable('guest_invoice_setting')) {
                Capsule::schema()->create('guest_invoice_setting', function ($table) {
                    $table->string('setting', 50);
                    $table->string('value', 50);
                    $table->primary('setting');
                });

                // Configuración inicial
                Capsule::table('guest_invoice_setting')->insert([
                    ['setting' => 'invoice_link_validity', 'value' => '24'],
                    ['setting' => 'viewInvoiceBtnEnable', 'value' => 'enabled'],
                    ['setting' => 'recaptchaEnable', 'value' => 'disabled']
                ]);
            }

            return ['status' => 'success'];
        } catch (Exception $e) {
            return ['status' => 'error', 'description' => $e->getMessage()];
        }
    }

    public function deactivate() {
        // No eliminamos tablas para mantener datos históricos
        return ['status' => 'success'];
    }

    public function getSetting($key) {
        $setting = Capsule::table('guest_invoice_setting')
            ->where('setting', $key)
            ->first();

        return $setting ? $setting->value : null;
    }

    public function updateSetting($key, $value) {
        Capsule::table('guest_invoice_setting')
            ->updateOrInsert(
                ['setting' => $key],
                ['value' => $value]
            );
    }

    public function logAction($action, $invoiceId, $clientId, $data = []) {
        Capsule::table('guest_invoice_logs')->insert([
            'module' => $this->moduleName,
            'action' => $action,
            'invoice_id' => $invoiceId,
            'client_id' => $clientId,
            'data' => json_encode($data),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function getModuleUrl(): string {
        return rtrim(Setting::getValue('SystemURL'), '/') . 
               '/modules/addons/' . $this->moduleName . '/guestinvoice.php';
    }

    public function getAjaxEndpoint(): string {
        return $this->getModuleUrl() . '?ajax_action=';
    }

    public function getSystemUrl(): string {
        return rtrim(Setting::getValue('SystemURL'), '/');
    }


    /**
     * Valida los parámetros para generar un link
     */
    private function validateGenerateLinkRequest(array $request): void
    {
        $required = ['invoice_id', 'client_id', 'duration', 'csrf_token'];
        foreach ($required as $field) {
            if (empty($request[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (!is_numeric($request['duration']) || $request['duration'] <= 0) {
            throw new InvalidArgumentException('Invalid duration value');
        }
    }

    /**
     * Valida que la vista solicitada sea válida
     */
    private function validateViewRequest(string $view): void
    {
        $validViews = ['dashboard', 'settings', 'activityHistory'];
        
        if (!in_array($view, $validViews)) {
            throw new InvalidArgumentException('Invalid view requested: ' . $view);
        }
    }

    private function formatErrorResponse(Exception $e): array
    {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            // Solo en desarrollo - quitar en producción
            'debug' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ];
    }
}