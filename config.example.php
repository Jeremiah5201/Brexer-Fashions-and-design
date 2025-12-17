<?php
// Example configuration for Brexers Fashions
// Copy this file to config.php and fill in real production credentials.

$hostName = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
$isLocal = $hostName === 'localhost' || $hostName === '127.0.0.1';

$db_host = $isLocal ? 'localhost' : 'your_remote_host';
$db_name = $isLocal ? 'brexers_fashion' : 'your_remote_db_name';
$db_user = $isLocal ? 'root' : 'your_remote_db_user';
$db_pass = $isLocal ? '' : 'your_remote_db_pass';
