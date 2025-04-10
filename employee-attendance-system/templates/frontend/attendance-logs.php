<div class="wrap eas-admin-wrap">
    <h1><?php esc_html_e('Employee Attendance Logs', 'employee-attendance'); ?></h1>
    
    <?php include EAS_PLUGIN_DIR . 'templates/admin/filters.php'; ?>
    
    <div class="eas-admin-content">
        <?php if (empty($logs)) : ?>
            <div class="notice notice-warning">
                <p><?php esc_html_e('No attendance records found.', 'employee-attendance'); ?></p>
            </div>
        <?php else : ?>
            <div class="eas-records-count">
                <?php printf(
                    esc_html(_n('%d record found', '%d records found', $total_records, 'employee-attendance')),
                    $total_records
                ); ?>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('User', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('Check In', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('Check Out', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('Status', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('Edits', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('Last Edited', 'employee-attendance'); ?></th>
                        <th><?php esc_html_e('Actions', 'employee-attendance'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log) : ?>
                        <?php $user = get_userdata($log->user_id); ?>
                        <tr>
                            <td><?php echo esc_html($log->id); ?></td>
                            <td>
                                <?php echo $user ? esc_html($user->display_name) : esc_html__('Unknown', 'employee-attendance'); ?>
                                <br>
                                <small><?php echo esc_html($user ? $user->user_email : ''); ?></small>
                            </td>
                            <td>
                                <?php echo esc_html(date_i18n('M j, Y g:i a', strtotime($log->check_in))); ?>
                            </td>
                            <td>
                                <?php echo $log->check_out ? esc_html(date_i18n('M j, Y g:i a', strtotime($log->check_out))) : '--:--'; ?>
                            </td>
                            <td>
                                <?php if ($log->check_out) : ?>
                                    <span class="status-completed"><?php esc_html_e('Completed', 'employee-attendance'); ?></span>
                                <?php else : ?>
                                    <span class="status-pending"><?php esc_html_e('Pending', 'employee-attendance'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo esc_html($log->edit_count); ?>
                            </td>
                            <td>
                                <?php if ($log->last_edited_at) : ?>
                                    <?php echo esc_html(date_i18n('M j, Y g:i a', strtotime($log->last_edited_at))); ?>
                                    <?php if ($log->last_edited_by) : ?>
                                        <br>
                                        <small>
                                            <?php 
                                            $editor = get_userdata($log->last_edited_by);
                                            echo esc_html($editor ? $editor->display_name : __('System', 'employee-attendance'));
                                            ?>
                                        </small>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <?php esc_html_e('Never', 'employee-attendance'); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="button edit-attendance-admin" 
                                        data-record="<?php echo esc_attr($log->id); ?>"
                                        data-checkin="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime($log->check_in))); ?>"
                                        data-checkout="<?php echo $log->check_out ? esc_attr(date('Y-m-d\TH:i', strtotime($log->check_out))) : ''; ?>">
                                    <?php esc_html_e('Edit', 'employee-attendance'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Admin Edit Modal -->
<div id="eas-admin-modal" class="eas-admin-modal">
    <div class="eas-admin-modal-content">
        <h3><?php esc_html_e('Edit Attendance Record', 'employee-attendance'); ?></h3>
        <form method="post" id="eas-admin-edit-form">
            <input type="hidden" name="record_id" id="eas-admin-record-id">
            <?php wp_nonce_field('eas_admin_edit_attendance', 'eas-admin-edit-nonce'); ?>
            
            <div class="eas-admin-form-group">
                <label for="eas-admin-check-in"><?php esc_html_e('Check In', 'employee-attendance'); ?></label>
                <input type="datetime-local" id="eas-admin-check-in" name="check_in" required>
            </div>
            
            <div class="eas-admin-form-group">
                <label for="eas-admin-check-out"><?php esc_html_e('Check Out', 'employee-attendance'); ?></label>
                <input type="datetime-local" id="eas-admin-check-out" name="check_out">
            </div>
            
            <div class="eas-admin-modal-actions">
                <button type="button" class="button eas-admin-cancel-edit"><?php esc_html_e('Cancel', 'employee-attendance'); ?></button>
                <button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'employee-attendance'); ?></button>
            </div>
        </form>
    </div>
</div>