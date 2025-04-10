<form method="get" class="eas-filters">
    <input type="hidden" name="page" value="employee-attendance">
    
    <div class="eas-filter-row">
        <div class="eas-filter-group">
            <label for="eas-from-date"><?php esc_html_e('From:', 'employee-attendance'); ?></label>
            <input type="date" id="eas-from-date" name="from" value="<?php echo esc_attr($from); ?>">
        </div>
        
        <div class="eas-filter-group">
            <label for="eas-to-date"><?php esc_html_e('To:', 'employee-attendance'); ?></label>
            <input type="date" id="eas-to-date" name="to" value="<?php echo esc_attr($to); ?>">
        </div>
        
        <div class="eas-filter-actions">
            <button type="submit" class="button button-primary">
                <?php esc_html_e('Filter', 'employee-attendance'); ?>
            </button>
            
            <?php if ($from || $to) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=employee-attendance')); ?>" class="button">
                    <?php esc_html_e('Reset', 'employee-attendance'); ?>
                </a>
            <?php endif; ?>
            
            <a href="<?php echo esc_url(add_query_arg([
                'page' => 'employee-attendance',
                'download_csv' => 1,
                'from' => $from,
                'to' => $to
            ])); ?>" class="button">
                <?php esc_html_e('Export CSV', 'employee-attendance'); ?>
            </a>
        </div>
    </div>
</form>