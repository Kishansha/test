<div class="employee-attendance-system">
    <?php if ($is_office) : ?>
        <div class="eas-wifi-status">
            ✔️ <?php _e('Connected to office network', 'employee-attendance'); ?>
        </div>
    <?php endif; ?>
    
    <?php echo $message; ?>
    
    <div class="attendance-status">
        <h4><?php _e("Today's Status:", 'employee-attendance'); ?></h4>
        <?php if ($current_status) : ?>
            <p>
                <?php if ($current_status->check_out) : ?>
                    ✔️ <?php _e('Completed:', 'employee-attendance'); ?>
                    <?php printf(__('In at %s - Out at %s', 'employee-attendance'), 
                        date_i18n(get_option('time_format'), strtotime($current_status->check_in)),
                        date_i18n(get_option('time_format'), strtotime($current_status->check_out))); ?>
                <?php else : ?>
                    ⏳ <?php printf(__('Checked in at %s (not out yet)', 'employee-attendance'),
                        date_i18n(get_option('time_format'), strtotime($current_status->check_in))); ?>
                <?php endif; ?>
            </p>
        <?php else : ?>
            <p>❌ <?php _e('Not checked in today', 'employee-attendance'); ?></p>
        <?php endif; ?>
    </div>

    <form method="post" class="attendance-actions">
        <?php wp_nonce_field('attendance_action', 'attendance_nonce'); ?>
        <button type="submit" name="attendance_action" value="Check In" class="button button-primary">
            <?php _e('Check In', 'employee-attendance'); ?>
        </button>
        <button type="submit" name="attendance_action" value="Check Out" class="button">
            <?php _e('Check Out', 'employee-attendance'); ?>
        </button>
    </form>

    <div class="attendance-history">
        <h4><?php _e('Your Recent Attendance:', 'employee-attendance'); ?></h4>
        <table>
            <thead>
                <tr>
                    <th><?php _e('Date', 'employee-attendance'); ?></th>
                    <th><?php _e('Check In', 'employee-attendance'); ?></th>
                    <th><?php _e('Check Out', 'employee-attendance'); ?></th>
                    <th><?php _e('Status', 'employee-attendance'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs) : ?>
                    <?php foreach ($logs as $log) : ?>
                        <tr>
                            <td><?php echo date_i18n(get_option('date_format'), strtotime($log->check_in)); ?></td>
                            <td><?php echo date_i18n(get_option('time_format'), strtotime($log->check_in)); ?></td>
                            <td><?php echo $log->check_out ? date_i18n(get_option('time_format'), strtotime($log->check_out)) : '--:--'; ?></td>
                            <td><?php echo $log->check_out ? __('Completed', 'employee-attendance') : __('Pending', 'employee-attendance'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4"><?php _e('No attendance records found', 'employee-attendance'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.eas-wifi-status {
    background: #e6ffed;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    color: #22863a;
}
.eas-wifi-error {
    background: #ffebee;
    padding: 15px;
    border-radius: 4px;
    color: #b71c1c;
}
</style>