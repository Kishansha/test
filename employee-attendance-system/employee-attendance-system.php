<?php
/**
 * Plugin Name: Employee Attendance System 2.0
 * Description: Allow employees to check in/out and admin to view logs.
 * Version: 1.5
 * Author: Kishan Kumar
 * Text Domain: employee-attendance
 */

defined('ABSPATH') || exit;

// Define constants
define('EAS_VERSION', '1.1');
define('EAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EAS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'EAS_';
    $base_dir = EAS_PLUGIN_DIR . 'includes/';
    
    if (strpos($class, $prefix) !== 0) return;
    
    $file = $base_dir . 'class-' . strtolower(str_replace([$prefix, '_'], ['', '-'], $class)) . '.php';
    if (file_exists($file)) require $file;
});

// Initialize
register_activation_hook(__FILE__, function() {
    EAS_Database::activate();
    EAS_Database::update_db();
});
add_action('plugins_loaded', ['EAS_Core', 'init']);