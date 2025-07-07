<?php
namespace GuestInvoice\Services;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use WHMCS\Database\Capsule;
use GuestInvoice\GuestInvoiceCore;
use GuestInvoice\Services\SecurityService;
use GuestInvoice\Services\LinkService;
use GuestInvoice\Services\ActivityHistoryService;
use GuestInvoice\GuestInvoiceUI;

class AjaxHandler 
{
    private $module;
    private $request;
    private $activityHistoryService;

    public function __construct(
        GuestInvoiceCore $module,
        ActivityHistoryService $activityHistoryService = null
    ) {
        $this->module = $module;
        $this->request = $_REQUEST;
        $this->activityHistoryService = $activityHistoryService ?? new ActivityHistoryService();
    }

    /**
     * Main AJAX request processor
     */
    public function process(): void 
    {
        try {
            $this->validateRequest();
            $response = $this->routeRequest();
            $this->sendResponse($response);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Route AJAX requests to appropriate handlers
     */
    private function routeRequest(): array 
    {
        $action = $this->request['ajax_action'] ?? '';
        
        $handlers = [
            'generate_link' => [$this, 'handleGenerateLink'],
            'load_view' => [$this, 'handleLoadView'],
            'log_details' => [$this, 'handleActivityHistoryDetails'],
            'save_settings' => [$this, 'handleSaveSettings'],
            'clear_logs' => [$this, 'handleClearLogs'],
            'clear_counters' => [$this, 'handleClearCounters'],
            'clear_all_links' => [$this, 'handleClearAllLinks'],
            // Add new handlers here
        ];

        if (!isset($handlers[$action])) {
            throw new InvalidArgumentException('Invalid AJAX action: ' . $action);
        }

        return $handlers[$action]();
    }

    /*****************************************************************
     * Request Handlers
     ****************************************************************/

    private function handleGenerateLink(): array
    {
        $this->validateRequiredFields(['invoice_id', 'client_id', 'duration']);
        
        $linkService = new LinkService();
        return $linkService->generateGuestLink($this->request);
    }

    private function handleLoadView(): array
    {
        $view = $this->request['view'] ?? 'dashboard';
        $this->validateView($view);
        
        $guestInvoice = GuestInvoiceUI::getInstance();
        return $guestInvoice->outputViewRender([], $view, true);
    }

    private function handleActivityHistoryDetails(): array
    {
        $logId = (int)($this->request['log_id'] ?? 0);
        if ($logId <= 0) {
            throw new InvalidArgumentException('Missing or invalid log_id parameter');
        }

        $log = Capsule::table('guest_invoice_logs')
            ->where('id', $logId)
            ->first();

        if (!$log) {
            throw new RuntimeException('Log entry not found', 404);
        }

        return [
            'success' => true,
            'log' => [
                'id' => $log->id,
                'action' => $log->action,
                'data' => $log->data ? json_decode($log->data, true) : null,
                'created_at' => $log->created_at
            ]
        ];
    }

    private function handleSaveSettings(): array
    {
        $this->validateRequiredFields(['default_duration', 'notification_email']);
        
        $settingsService = new SettingsService($this->activityHistoryService);
        $settingsService->processSettingsForm($this->request);

        $guestInvoice = GuestInvoiceUI::getInstance();
        return $guestInvoice->outputViewRender(
            ['modulelink' => $this->module->getModuleUrl()], 
            'settings', 
            true
        );
    }

    /*****************************************************************
     * Validation Methods
     ****************************************************************/

    private function validateRequest(): void 
    {
        if (!SecurityService::validateCSRFToken($this->request['csrf_token'] ?? '')) {
            throw new Exception('Invalid CSRF token', 403);
        }
        
        if (!isset($this->request['ajax_action'])) {
            throw new Exception('Missing action parameter', 400);
        }
    }

    private function validateRequiredFields(array $fields): void
    {
        foreach ($fields as $field) {
            if (empty($this->request[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }

    private function validateView(string $view): void
    {
        $validViews = ['dashboard', 'settings', 'activityHistory', 'generate'];
        
        if (!in_array($view, $validViews)) {
            throw new InvalidArgumentException('Invalid view requested: ' . $view);
        }
    }

    /*****************************************************************
     * Response Methods
     ****************************************************************/

    private function sendResponse(array $data, int $statusCode = 200): void 
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function handleException(Exception $e): void 
    {
        $this->logError($e);
        
        // In production, don't expose error details
        if ($this->isProduction()) {
            // Log the error and show a generic message
            logActivity('Guest Invoice Error: ' . $e->getMessage());
            if (!defined('WHMCS')) die;
            die('An error occurred. Please try again.');
        }
        
        // In development, show detailed error
        $this->sendResponse([
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ], $this->getHttpStatusCode($e));
    }
    
    /**
     * Check if we're in production environment
     */
    private function isProduction(): bool
    {
        // Check if we're in WHMCS admin area
        if (defined('ADMINAREA') && ADMINAREA) {
            return true;
        }
        
        // Additional checks can be added here if needed
        return true; // Default to production for safety
    }

    private function logError(Exception $e): void 
    {
        $this->activityHistoryService->logActivity(
            'ajax_error',
            [
                'action' => $this->request['ajax_action'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        );
    }

    private function handleClearLogs(): array
    {
        if (!isset($_SESSION['adminid'])) {
            throw new Exception('Unauthorized', 403);
        }

        $success = ActivityHistoryService::clearActivityLogs();
        
        return [
            'success' => $success,
            'message' => $success ? 'Activity logs cleared successfully' : 'Failed to clear activity logs'
        ];
    }

    public function handleClearCounters(): array
    {
        if (!isset($_SESSION['adminid'])) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $result = ActivityHistoryService::clearAccessCounters();
        
        return [
            'success' => $result,
            'message' => $result 
                ? 'Access counters cleared successfully' 
                : 'Failed to clear access counters'
        ];
    }
    
    /**
     * Handle clear all guest invoice links request
     */
    public function handleClearAllLinks(): array
    {
        if (!isset($_SESSION['adminid'])) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $result = ActivityHistoryService::clearAllGuestLinks();
        
        return [
            'success' => $result,
            'message' => $result 
                ? 'All guest invoice links have been cleared successfully' 
                : 'Failed to clear guest invoice links'
        ];
    }

    private function getHttpStatusCode(Exception $e): int 
    {
        return $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    }
}