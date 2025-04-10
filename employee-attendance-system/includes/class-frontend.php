<?php
class EAS_Frontend {
    public static function init() {
        add_shortcode('employee_attendance', [__CLASS__, 'attendance_shortcode']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    protected static function is_office_network() {
        $allowed_ips = [
            '103.211.14.80',       // Replace with office IP
                   // Office subnet
                // Backup IP
        ];

        $user_ip = $_SERVER['REMOTE_ADDR'];

        foreach ($allowed_ips as $ip) {
            if (strpos($user_ip, $ip) === 0) {
                return true;
            }
        }
        return false;
    }

    public static function enqueue_assets() {
        wp_enqueue_style('eas-frontend', EAS_PLUGIN_URL . 'assets/css/style.css', [], EAS_VERSION);
    }

    public static function attendance_shortcode() {
        // Office WiFi check
        if (!self::is_office_network()) {
            return '<div class="eas-wifi-error">'.
                   __('⚠️ Connect to office WiFi to access attendance.', 'employee-attendance').
                   '</div>';
        }

        if (!is_user_logged_in()) {
            return '<div class="attendance-notice">'.
                   __('Please login to mark attendance.', 'employee-attendance').
                   '</div>';
        }

        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        $user_id = get_current_user_id();
        $today = date('Y-m-d');
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_action'])) {
            $action = sanitize_text_field($_POST['attendance_action']);
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d AND DATE(check_in) = %s",
                $user_id, $today
            ));

            if ($action === 'Check In') {
                if ($existing) {
                    $message = $existing->check_out 
                        ? '<div class="attendance-warning">'.__('Already completed attendance today', 'employee-attendance').'</div>'
                        : '<div class="attendance-warning">'.sprintf(__('Already checked in at %s', 'employee-attendance'), date_i18n(get_option('time_format'), strtotime($existing->check_in))).'</div>';
                } else {
                    $inserted = $wpdb->insert($table, [
                        'user_id' => $user_id,
                        'check_in' => current_time('mysql')
                    ]);
                    $message = $inserted
                        ? '<div class="attendance-success">'.sprintf(__('Checked in at %s', 'employee-attendance'), date_i18n(get_option('time_format'))).'</div>'
                        : '<div class="attendance-error">'.__('System error. Please try again.', 'employee-attendance').'</div>';
                }
            }

            if ($action === 'Check Out') {
                if (!$existing) {
                    $message = '<div class="attendance-error">'.__('Please check in first.', 'employee-attendance').'</div>';
                } elseif ($existing->check_out) {
                    $message = '<div class="attendance-warning">'.sprintf(__('Already checked out at %s', 'employee-attendance'), date_i18n(get_option('time_format'), strtotime($existing->check_out))).'</div>';
                } else {
                    $updated = $wpdb->update($table,
                        ['check_out' => current_time('mysql')],
                        ['id' => $existing->id]
                    );
                    $message = $updated
                        ? '<div class="attendance-success">'.sprintf(__('Checked out at %s', 'employee-attendance'), date_i18n(get_option('time_format'))).'</div>'
                        : '<div class="attendance-error">'.__('System error. Please try again.', 'employee-attendance').'</div>';
                }
            }
        }

        $current_status = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND DATE(check_in) = %s",
            $user_id, $today
        ));

        $logs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY check_in DESC LIMIT 10",
            $user_id
        ));

        return self::render_template('frontend/attendance-form', [
            'message' => $message,
            'current_status' => $current_status,
            'logs' => $logs,
            'is_office' => true // Flag for WiFi indicator
        ]);
    }

    protected static function render_template($template, $data = []) {
        extract($data);
        ob_start();
        include EAS_PLUGIN_DIR . "templates/{$template}.php";
        return ob_get_clean();
    }
}