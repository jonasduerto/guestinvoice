<?php
namespace GuestInvoice\Services;

use WHMCS\Database\Capsule;
use WHMCS\Session;
use GuestInvoice\Services\SecurityService;
use GuestInvoice\Services\ActivityHistoryService;
use Exception;

class SettingsService
{
    const SETTINGS_TABLE = 'guest_invoice_setting';
    const VALID_DURATIONS = [1, 4, 12, 24];
    
    private $activityHistoryService;

    public function __construct(ActivityHistoryService $activityHistoryService = null)
    {
        $this->activityHistoryService = $activityHistoryService ?? new ActivityHistoryService();
    }

    /**
     * Obtiene los datos de configuración para la vista
     */
    public function getSettingsData(): array
    {
        return [
            'defaultDuration'    => $this->getSettingValue('invoice_link_validity', 24),
            'sendEmailDefault'   => $this->getSettingValue('send_email_default') === 'enabled',
            'notificationEmail'  => $this->getSettingValue('notification_email', ''),
            'totalLinks'         => $this->getTotalLinksCount(),
            'activeLinks'       => $this->getActiveLinksCount(),
            'totalAccesses'     => $this->getTotalAccessesCount()
        ];
    }

    /**
     * Procesa el formulario de configuración
     */
    public function processSettingsForm(array $data): void
    {
        if (!SecurityService::validateCSRFToken($data['csrf_token'] ?? '')) {
            throw new Exception('Invalid CSRF token. Please refresh the page and try again.');
        }

        $this->validateSettings($data);
        $this->saveSettings($data);
        $this->logSettingsChange($data);
    }

    /**
     * Valida los datos del formulario de configuración
     */
    public function validateSettings(array $data): void
    {
        $duration = (int)($data['default_duration'] ?? 0);
        if (!in_array($duration, self::VALID_DURATIONS, true)) {
            throw new Exception('Invalid duration value. Allowed values: ' . implode(', ', self::VALID_DURATIONS));
        }

        $email = trim($data['notification_email'] ?? '');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid notification email address');
        }
    }

    /**
     * Registra cambios en la configuración
     */
    public function logSettingsChange(array $data): void
    {
        $this->activityHistoryService->logActivity(
            'settings_update',
            [
                'admin_id' => Session::get('adminid') ?? 0,
                'user_id' => Session::get('uid') ?? 0,
                'changes' => [
                    'default_duration'   => $data['default_duration'] ?? null,
                    'send_email_default' => isset($data['send_email_default']),
                    'notification_email' => $data['notification_email'] ?? null,
                ]
            ]
        );
    }

    /*****************************************************************
     * Private Helper Methods
     ****************************************************************/

    private function getSettingValue(string $key, $default = null)
    {
        $setting = Capsule::table(self::SETTINGS_TABLE)
            ->where('setting', $key)
            ->value('value');

        return $setting ?? $default;
    }

    private function saveSettings(array $data): void
    {
        Capsule::table(self::SETTINGS_TABLE)->updateOrInsert(
            ['setting' => 'invoice_link_validity'],
            ['value' => (int)($data['default_duration'] ?? 24)]
        );

        Capsule::table(self::SETTINGS_TABLE)->updateOrInsert(
            ['setting' => 'send_email_default'],
            ['value' => isset($data['send_email_default']) ? 'enabled' : 'disabled']
        );

        Capsule::table(self::SETTINGS_TABLE)->updateOrInsert(
            ['setting' => 'notification_email'],
            ['value' => trim($data['notification_email'] ?? '')]
        );
    }

    private function getTotalLinksCount(): int
    {
        return Capsule::table('guest_invoice')->count();
    }

    private function getActiveLinksCount(): int
    {
        return Capsule::table('guest_invoice')->where('status', 1)->count();
    }

    private function getTotalAccessesCount(): int
    {
        return Capsule::table('guest_invoice')->sum('access_count') ?? 0;
    }
}