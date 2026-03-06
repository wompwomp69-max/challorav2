<?php
/**
 * Email configuration (untuk pengiriman otomatis)
 * Sesuaikan dengan SMTP Anda (Gmail, Outlook, dll)
 * Gmail: gunakan App Password, bukan password biasa
 */


return [
    'enabled' => true,  // set true untuk mengaktifkan pengiriman otomatis
    'from_email' => 'noreply@challora.id',
    'from_name' => 'Challora Recruitment',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'noreply@challora.id',
    'smtp_pass' => 'AdukAduk2024',  // App Password untuk Gmail
    'smtp_secure' => 'tls',
];
