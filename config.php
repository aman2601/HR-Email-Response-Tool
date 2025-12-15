<?php
// SMTP configuration - update these values for your environment.
// You should keep credentials secure and out of version control in real projects.
return [
    'smtp_host' => 'smtp.example.com',
    'smtp_port' => 587,
    'smtp_user' => 'your_smtp_user@example.com',
    'smtp_pass' => 'your_smtp_password',
    'smtp_secure' => 'tls', // 'ssl' or 'tls' or ''
    'from_email' => 'hr@yourcompany.com',
    'from_name' => 'HR Team'
];
