# GuestInvoice WHMCS Addon

GuestInvoice is a WHMCS addon module that provides secure, temporary guest access to invoices. Built with modern PHP practices, it offers a robust and maintainable solution for sharing invoice access without requiring client logins.

## 🚀 Features

- **Secure Guest Access**: Generate temporary guest invoice links with configurable expiration
- **Modern OOP Architecture**: Fully refactored with proper namespacing and design patterns
- **Comprehensive Logging**: Track all access attempts and actions
- **Admin Dashboard**: View metrics, manage logs, and configure settings
- **Email Notifications**: Optional email alerts for generated links
- **Responsive UI**: Works on all devices
- **CSRF Protection**: Built-in security measures
- **WHMCS 8.x+ Ready**: Fully compatible with modern WHMCS versions

## 📦 Installation

1. Copy the `guestinvoice` folder to your WHMCS `modules/addons/` directory
2. Log in to your WHMCS admin area
3. Navigate to Setup > Addon Modules
4. Locate "GuestInvoice" and click "Activate"
5. Configure the module settings as needed

## 🛠️ Project Structure

```
/modules/addons/guestinvoice/
├── Services/                     # Service classes
│   ├── ActivityHistoryService.php # Activity history management
│   ├── AjaxHandler.php           # AJAX request handling
│   ├── DashboardService.php      # Dashboard metrics and data
│   ├── EmailService.php          # Email notifications
│   ├── LinkService.php           # Link generation and management
│   ├── SecurityService.php       # Security and validation
│   ├── SessionService.php        # Session management
│   └── SettingsService.php       # Module settings
├── assets/                      # Frontend assets
│   ├── css/                     # Stylesheets
│   │   └── guestinvoice.css     # Main styles
│   ├── img/                     # Images and icons
│   │   ├── ft_logo.png
│   │   ├── hometest-logo.svg
│   │   ├── logo.png
│   │   └── languages.svg
│   └── js/                      # JavaScript files
│       ├── activityHistory.js   # Activity history functionality
│       ├── admin.js             # Admin interface scripts
│       ├── app.js               # Main application scripts
│       ├── guest-invoice.js     # Guest invoice functionality
│       └── guestlink_modal.js   # Modal dialog handling
├── templates/                   # Template files
│   ├── emails/                  # Email templates
│   │   └── guest_invoice_link.tpl
│   ├── pages/                   # Page templates
│   │   ├── activityHistory.tpl  # Activity history view
│   │   ├── dashboard.tpl        # Admin dashboard
│   │   └── settings.tpl         # Module settings
│   ├── error.tpl                # Error page template
│   ├── guestlink_modal.tpl      # Guest link modal dialog
│   └── master.tpl               # Main layout template
├── lang/                       # Language files
│   ├── english.php
│   └── spanish.php
├── GuestInvoiceCore.php        # Core module functionality
├── GuestInvoiceUI.php          # UI components and rendering
├── autoload.php               # Class autoloader
├── bootstrap.php              # Application bootstrap
├── guestinvoice.php           # Main module file
├── hooks.php                  # WHMCS hooks
└── whmcs.json                 # WHMCS module metadata
```

## 🔒 Security Features

- Secure token-based authentication
- Configurable link expiration
- CSRF protection
- Rate limiting
- IP-based access controls (if implemented in SecurityService)
- Detailed access logging
- Automatic session management

## 🔧 Requirements

- WHMCS 8.x or later
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+

## 🚀 Getting Started

1. **Generate a Guest Link**:
   - Navigate to an invoice in the client area or admin
   - Click "Generate Guest Link"
   - Select expiration time and copy the generated link

2. **Admin Dashboard**:
   - Access via Setup > Addon Modules > GuestInvoice
   - View access statistics
   - Manage active links
   - Configure module settings

## 📝 License

MIT License

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📬 Support

For support, please open an issue in the GitHub repository.