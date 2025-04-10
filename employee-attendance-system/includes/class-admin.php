<?php
class EAS_Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_ajax_eas_admin_edit', [__CLASS__, 'handle_admin_edit']);
    }

    public static function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_employee-attendance') return;
        
        wp_enqueue_style('eas-admin', EAS_PLUGIN_URL . 'assets/css/admin.css', [], EAS_VERSION);
        wp_enqueue_script('eas-admin', EAS_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], EAS_VERSION, true);
        
        wp_localize_script('eas-admin', 'easAdminData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('eas_admin_nonce')
        ]);
    }

    public static function add_admin_menu() {
        add_menu_page(
            __('Employee Attendance', 'employee-attendance'),
            __('Attendance', 'employee-attendance'),
            'manage_options',
            'employee-attendance',
            [__CLASS__, 'render_admin_page'],
            'dashicons-clipboard',
            25
        );
    }

    public static function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.', 'employee-attendance'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        
        if (isset($_GET['download_csv'])) {
            self::export_csv();
            return;
        }

        $from = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : '';
        $to = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : '';
        $logs = self::get_filtered_logs($from, $to);

        include EAS_PLUGIN_DIR . 'templates/admin/attendance-logs.php';
    }

    public static function handle_admin_edit() {
        check_ajax_referer('eas_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        
        $data = [
            'check_in'        => sanitize_text_field($_POST['check_in']),
            'check_out'       => sanitize_text_field($_POST['check_out']),
            'last_edited_by'  => get_current_user_id(),
            'last_edited_at'  => current_time('mysql'),
            'edited_by_admin' => 1
        ];

        $result = $wpdb->update(
            $table,
            $data,
            ['id' => intval($_POST['id'])]
        );

        wp_send_json(['success' => $result !== false]);
    }

    protected static function get_filtered_logs($from, $to) {
        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        $where = [];
        $params = [];
        
        if ($from && $to) {
            $where[] = 'DATE(check_in) BETWEEN %s AND %s';
            $params[] = $from;
            $params[] = $to;
        }
        
        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT * FROM {$table} {$where_clause} ORDER BY check_in DESC";
        
        return $params ? $wpdb->get_results($wpdb->prepare($query, $params)) : $wpdb->get_results($query);
    }

    protected static function export_csv() {
        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        $from = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : '';
        $to = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : '';
        $logs = self::get_filtered_logs($from, $to);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=attendance_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // UTF-8 BOM
        
        fputcsv($output, [
            'ID',
            'User',
            'Check In',
            'Check Out',
            'Status',
            'Last Edited By',
            'Last Edited At'
        ]);
        
        foreach ($logs as $log) {
            $user = get_userdata($log->user_id);
            $editor = $log->last_edited_by ? get_userdata($log->last_edited_by) : null;
            
            fputcsv($output, [
                $log->id,
                $user ? $user->display_name : 'Unknown',
                $log->check_in,
                $log->check_out ?: 'N/A',
                $log->check_out ? 'Completed' : 'Pending',
                $editor ? $editor->display_name : 'N/A',
                $log->last_edited_at ?: 'Never'
            ]);
        }
        
        fclose($output);
        exit;
    }
}