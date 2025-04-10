<?php
class EAS_Database {
    public static function activate() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'employee_attendance';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            check_in datetime DEFAULT NULL,
            check_out datetime DEFAULT NULL,
            edit_count tinyint(1) DEFAULT 0,
            last_edited_by bigint(20) DEFAULT NULL,
            last_edited_at datetime DEFAULT NULL,
            notes text DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX user_date_idx (user_id, check_in)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public static function update_db() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'employee_attendance';
        
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
        $columns = wp_list_pluck($columns, 'Field');
        
        $alter_sql = [];
        
        if (!in_array('edit_count', $columns)) {
            $alter_sql[] = "ADD COLUMN edit_count tinyint(1) DEFAULT 0";
        }
        
        if (!in_array('last_edited_by', $columns)) {
            $alter_sql[] = "ADD COLUMN last_edited_by bigint(20) DEFAULT NULL";
        }
        
        if (!in_array('last_edited_at', $columns)) {
            $alter_sql[] = "ADD COLUMN last_edited_at datetime DEFAULT NULL";
        }
        
        if (!empty($alter_sql)) {
            $wpdb->query("ALTER TABLE $table_name " . implode(', ', $alter_sql));
        }
    }
    
    public static function get_attendance($user_id = null, $date = null, $limit = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        
        $where = [];
        $params = [];
        
        if ($user_id) {
            $where[] = 'user_id = %d';
            $params[] = $user_id;
        }
        
        if ($date) {
            $where[] = 'DATE(check_in) = %s';
            $params[] = $date;
        }
        
        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT * FROM $table $where_clause ORDER BY check_in DESC";
        
        if ($limit) {
            $query .= $wpdb->prepare(" LIMIT %d", $limit);
        }
        
        return $params ? $wpdb->get_results($wpdb->prepare($query, $params)) : $wpdb->get_results($query);
    }
    
    public static function update_attendance($data, $where) {
        global $wpdb;
        $table = $wpdb->prefix . 'employee_attendance';
        return $wpdb->update($table, $data, $where);
    }
}