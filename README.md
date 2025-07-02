
# GuestInvoice WHMCS Addon

GuestInvoice is a WHMCS addon module that allows you to generate secure, temporary guest access links to invoices. Clients or admins can share these links so that invoices can be viewed and paid without requiring a login. All access is logged, and links can be configured to expire automatically.

## 📊 Project Language Stats

<p align="center">
  <img src="./assets/languages.svg" alt="Project Language Stats" width="400"/>
</p>

## Features
- Generate temporary guest invoice links (1h, 4h, 12h, 24h, or custom)
- Secure token-based access with expiration
- Logs all accesses and actions
- Admin dashboard with metrics and logs
- Configurable settings (link validity, button visibility, etc.)
- Email notification support
- No template or JS/CSS file modifications required

## Installation
1. Copy the `guestinvoice` folder to your `modules/addons/` directory in your WHMCS installation.
2. Activate the module from the WHMCS admin area (Setup > Addon Modules > GuestInvoice > Activate).
3. Upon activation, the module will automatically create the required database tables (`guest_invoice`, `guest_invoice_logs`, `guest_invoice_setting`).
4. Configure the module settings as needed from the admin area.

## Folder Structure
```
modules/addons/guestinvoice/
├── guestinvoice.php                # Main module file (activation, config, admin output)
├── hooks.php                       # Hooks for client area integration and AJAX
├── lib/
│   └── Admin/
│       ├── AdminDispatcher.php     # Admin dispatcher for panel routing
│       └── Controller.php         # Admin controller for dashboard, logs, settings
├── include/
│   ├── ajaxfile.php               # (Optional) AJAX endpoint for admin actions
│   └── wgs_mailer.php             # (Optional) Email helper
├── templates/
│   └── admin/
│       ├── guestInvoice.tpl       # Admin dashboard/settings template
│       ├── logs.tpl               # Admin logs template
│       └── ...
├── assests/
│   ├── js/                        # JavaScript files (do not modify)
│   └── css/                       # CSS files (do not modify)
├── lang/
│   └── english.php                # Language file (do not modify)
└── includes/
    └── guestinvoice.php           # Guest access handler (link validation, session)
```

## Activation & Deactivation
- **Activation:** The module will create all required tables and insert default settings. No manual SQL is needed.
- **Deactivation:** By default, tables are not dropped. You can enable table removal by uncommenting the lines in `guestinvoice_deactivate()`.

## Usage
- After activation, a "Guest Invoice" button will appear on invoice pages (if enabled in settings).
- Admins can view metrics, logs, and change settings from the module admin panel.
- All guest accesses and actions are logged for auditing.

## Security
- All guest links use secure, random tokens and have configurable expiration.
- Only the invoice owner (client) or admin can generate links.
- All access is logged in the `guest_invoice_logs` table.

## Requirements
- WHMCS 8.x or later
- PHP 7.2+

## License
MIT

## Credits
Based on the official [WHMCS Sample Addon Module](https://github.com/WHMCS/sample-addon-module) and best practices from the WHMCS developer documentation. 