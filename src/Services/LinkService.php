<?php
namespace GuestInvoice\Services;

use WHMCS\Database\Capsule;
use WHMCS\Config\Setting;
use WHMCS\User\Client;
use Carbon\Carbon;
use Exception;

class LinkService {
    const TABLE_NAME = 'guest_invoice';
    
    /**
     * Generate a guest access link for an invoice
     */
    /**
     * @var EmailService
     */
    protected $emailService;

    public function __construct(EmailService $emailService = null) {
        $this->emailService = $emailService ?? new EmailService();
    }
    
    public function generateGuestLink(array $data): array {
        $this->validateLinkData($data);
        
        $adminId = $this->getAdminId($data);
        $invoiceId = (int)$data['invoice_id'];
        $clientId = (int)$data['client_id'];
        $validHours = $this->getValidHours($data);
        
        $token = $this->generateToken();
        $validTime = time() + ($validHours * 3600);
        $expiryDate = date('Y-m-d H:i', $validTime);
        $link = $this->buildGuestUrl($token, $invoiceId);
        
        $this->storeGuestLink($adminId, $clientId, $invoiceId, $token, $validTime, $link);
        
        // Send email to client if requested
        if (!empty($data['send_to_client']) && $data['send_to_client'] === '1') {
            $this->emailService->sendGuestLinkToClient(
                $clientId, 
                $invoiceId, 
                $link, 
                $expiryDate
            );
        }
        
        // Send test email if test email is provided
        if (!empty($data['test_email'])) {
            $this->emailService->sendTestGuestLink(
                $data['test_email'],
                $invoiceId,
                $link,
                $expiryDate
            );
        }
        
        return [
            'success' => true,
            'link' => $link,
            'expires' => $expiryDate
        ];
    }
    
    /**
     * Generate a secure token for guest access
     */
    public function generateToken(): string {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Build the guest access URL
     */
    public function buildGuestUrl(string $token, int $invoiceId): string
    {
        $systemUrl = rtrim(Setting::getValue('SystemURL'), '/');
        return "{$systemUrl}/guestinvoice/{$invoiceId}/{$token}";
    }
    
    /**
     * Get valid link information from database
     */
    public function getValidLink(string $token): ?object {
        return Capsule::table(self::TABLE_NAME)
            ->where('authid', $token)
            ->where('status', 1)
            ->where('validtime', '>', time())
            ->first();
    }
    
    /**
     * Record access to a guest link
     */
    public function recordAccess(int $tokenId): void {
        Capsule::table(self::TABLE_NAME)
            ->where('id', $tokenId)
            ->update([
                'access_count' => Capsule::raw('access_count + 1'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }
    
    /**
     * Get the default validity duration from settings
     */
    public function getDefaultValidityHours(): int {
        $hours = (int)Capsule::table('guest_invoice_setting')
            ->where('setting', 'invoice_link_validity')
            ->value('value');
            
        return max(1, $hours ?: 24); // Default to 24 hours if not set
    }
    
    /**
     * Validate required fields for link generation
     */
    private function validateLinkData(array $data): void {
        $requiredFields = ['invoice_id', 'client_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
    }
    
    /**
     * Get admin ID from session or data
     */
    private function getAdminId(array $data): int {
        $adminId = (int)($data['admin_id'] ?? $_SESSION['adminid'] ?? 0);
        if ($adminId <= 0) {
            throw new Exception('Admin ID is required');
        }
        return $adminId;
    }
    
    /**
     * Get valid hours from settings or data
     */
    private function getValidHours(array $data): int {
        $validHours = isset($data['valid_hours']) && is_numeric($data['valid_hours']) 
            ? (int)$data['valid_hours'] 
            : $this->getDefaultValidityHours();
            
        return max(1, $validHours);
    }
    
    public function validateGuestLink(int $invoiceId, string $token) {
        $link = Capsule::table('guest_invoice')
            ->where('invoiceId', $invoiceId)
            ->where('authid', $token)
            ->where('status', 1)
            ->where('validtime', '>', time())
            ->first();
            
        if (!$link) {
            throw new Exception('Invalid or expired link');
        }
        
        return $link;
    }
    
    /**
     * Store the guest link in database
     */
    private function storeGuestLink(int $adminId, int $clientId, int $invoiceId, 
                                 string $token, int $validTime, string $link): void {
        Capsule::table(self::TABLE_NAME)->insert([
            'userId' => $adminId,
            'clientId' => $clientId,
            'invoiceId' => $invoiceId,
            'authid' => $token,
            'validtime' => $validTime,
            'referralLink' => $link,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}