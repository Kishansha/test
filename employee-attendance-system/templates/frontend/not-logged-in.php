<div class="eas-notice">
    <p><?php esc_html_e('Please log in to mark your attendance.', 'employee-attendance'); ?></p>
    <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="button">
        <?php esc_html_e('Login', 'employee-attendance'); ?>
    </a>
</div>