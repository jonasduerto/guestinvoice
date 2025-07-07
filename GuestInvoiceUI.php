<?php
namespace GuestInvoice;

use WHMCS\Smarty;
use WHMCS\Session;
use WHMCS\Exception;
use WHMCS\Database\Capsule;
use WHMCS\Config\Setting;
use GuestInvoice\Services\LinkService;
use GuestInvoice\Services\ActivityHistoryService;
use GuestInvoice\Services\SessionService;
use GuestInvoice\Services\SecurityService;
use GuestInvoice\Services\DashboardService;
use GuestInvoice\Services\SettingsService;

class GuestInvoiceUI extends GuestInvoiceCore 
{
    protected $activityHistoryService;
    protected $dashboardService;
    protected $settingsService;
    protected $linkService;
    protected $smarty;
    public $lang;

    public function __construct(
        ActivityHistoryService $activityHistoryService = null,
        DashboardService $dashboardService = null,
        SettingsService $settingsService = null,
        LinkService $linkService = null
    ) {
        $this->activityHistoryService = $activityHistoryService ?? new ActivityHistoryService();
        $this->dashboardService = $dashboardService ?? new DashboardService($this->activityHistoryService);
        $this->settingsService = $settingsService ?? new SettingsService($this->activityHistoryService);
        $this->linkService = $linkService ?? new LinkService();
    }

    /**
     * Initialize Smarty template engine
     */
    public function getSmartyInstance(): Smarty 
    {
        if (!$this->smarty) {
            $this->smarty = new Smarty();
            $this->smarty->caching = false;
            $this->smarty->compile_dir = $GLOBALS['templates_compiledir'];
            $this->smarty->setTemplateDir(__DIR__ . '/templates/');
            
            $lang = $this->getLanguageStrings();
            $this->smarty->assign('LANG', $lang);
            $this->smarty->assign('_lang', $lang);
            $this->smarty->assign('_LANG', $lang);
            $this->smarty->assign('_ADDONLANG', $lang);
        }
        return $this->smarty;
    }

    /**
     * Main rendering method
     */
    public function outputViewRender($vars, $view = 'dashboard', $isAjax = false) 
    {
        // var_dump($view);
        try {
            $this->smarty = $this->getSmartyInstance();
            $this->assignBaseVariables($vars, $view);

            if ($isAjax) {
                return $this->handleAjaxRequest($view, $vars);
            }

            return $this->smarty->display('master.tpl');
        } catch (Exception $e) {
            return $this->handleError($e, $isAjax);
        }
    }

    /**
     * Load language strings
     */
    public function getLanguageStrings(): array 
    {
        if (!empty($this->lang)) {
            return $this->lang;
        }

        $language = $_SESSION['Language'] ?? 'english';
        $langFile = __DIR__ . "/lang/{$language}.php";
        
        if (file_exists($langFile)) {
            // Include the language file which should set $_ADDONLANG
            include $langFile;
            $this->lang = $_ADDONLANG ?? [];
        } else {
            // Fallback to English
            include __DIR__ . "/lang/english.php";
            $this->lang = $_ADDONLANG ?? [];
        }

        return $this->lang;
    }

    /**
     * Decorate client area for guest sessions
     */
    public function decorateGuestInterface(array $vars): array 
    {
        if (!SessionService::isGuestSession()) {
            return $vars;
        }

        $minutes = SecurityService::getRemainingSessionTime();

        $vars['headoutput'] .= "<style>
            #main-menu, .client-area-sidebar { display: none; }
            .guest-warning { background: #fff8e1; padding: 15px; margin-bottom: 20px; }
        </style>";

        $vars['breadcrumbnav'] = '<div class="guest-warning">
            <i class="fas fa-clock"></i> Sesión temporal expira en ' . $minutes . ' minutos
        </div>' . $vars['breadcrumbnav'];

        return $vars;
    }
    public function getClientIdByInvoiceId(int $invoiceId): ?int
    {
        $clientId = Capsule::table('tblinvoices')
            ->where('id', $invoiceId)
            ->value('userid');
        return $clientId ? (int)$clientId : null;
    }
    /*****************************************************************
     * Private Methods
     ****************************************************************/

    private function assignBaseVariables(array $vars, string $view): void
    {
        $vars = array_merge($vars, $this->prepareBaseVars($vars));
        $vars = array_merge($vars, [
            '_lang' => $this->getLanguageStrings(),
            'currentPage' => $view,
            'csrfToken' => SecurityService::getCSRFToken(),
            'modulelink' => $vars['modulelink'],
            'breadcrumb' => $this->dashboardService->getBreadcrumb($view, $this->getLanguageStrings())
        ]);

        // View-specific data
        switch ($view) {
            case 'dashboard':
                $vars = array_merge($vars, $this->dashboardService->getDashboardData());
                break;
            case 'settings':
                $vars = array_merge($vars, $this->settingsService->getSettingsData());
                break;
            case 'activityHistory':
                $vars = array_merge($vars, $this->activityHistoryService->getActivityHistoryData());
                break;
        }

        $this->smarty->assign($vars);
    }

    private function prepareBaseVars(array $vars): array
    {
        $assetPath = '../modules/addons/' . $this->moduleName . '/assets/';
        
        // Add JavaScript files in correct order
        $jsFiles = [
            'js/app.js',            // Main SPA application
            'js/admin.js'           // Admin-specific functionality (includes pagination)
        ];
        
        $jsIncludes = [];
        foreach ($jsFiles as $jsFile) {
            $jsPath = $assetPath . $jsFile;
            $jsIncludes[] = '<script type="text/javascript" src="' . $jsPath . '?v=' . time() . '"></script>';
        }
        
        return array_merge($vars, [
            'modulelink' => $vars['modulelink'],
            'systemurl' => rtrim(Setting::getValue('SystemURL'), '/'),
            'assetPath' => $assetPath,
            'js_includes' => implode("\n", $jsIncludes)
        ]);
    }

    private function handleAjaxRequest(string $view, array $vars): array
    {
        $template = "pages/{$view}.tpl";
        $html = $this->smarty->fetch($template);
        
        return [
            'success' => true,
            'html' => $html,
            'breadcrumb' => $vars['breadcrumb'] ?? ''
        ];
    }

    private function handleError(Exception $e, bool $isAjax)
    {
        $this->activityHistoryService->logActivity("GuestInvoice Error: " . $e->getMessage());
        
        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }

        $this->smarty->assign('error', $e->getMessage());
        return $this->smarty->display('error.tpl');
    }
}