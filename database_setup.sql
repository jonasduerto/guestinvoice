-- Tabla para almacenar los enlaces temporales de facturas
CREATE TABLE `guest_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL,
  `authid` varchar(255) NOT NULL,
  `validtime` int(11) NOT NULL,
  `referralLink` longtext NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*!40000 ALTER TABLE `guest_invoice` DISABLE KEYS */;
-- Tabla para logs de acceso
CREATE TABLE `guest_invoice_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `module` varchar(255) DEFAULT NULL,
  `action` text NOT NULL,
  `request` mediumtext DEFAULT NULL,
  `response` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=738 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Tabla para configuración del módulo
CREATE TABLE `guest_invoice_setting` (
  `setting` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Insertar configuración por defecto
INSERT INTO `guest_invoice_setting` VALUES
('enable_invoice_btn','enabled'),
('view_invoice_count','enabled'),
('template','Guest Invoice'),
('view_on_adminside','enabled'),
('invoice_link','enabled'),
('invoice_link_validity','24'),
('guest_invoice','1'),
('invoice_template','Invoice Created'),
('recaptchaEnable','disabled'),
('viewInvoiceBtnEnable','enabled');
