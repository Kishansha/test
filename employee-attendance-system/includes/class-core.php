<?php
class EAS_Core {
    public static function init() {
        load_plugin_textdomain(
            'employee-attendance',
            false,
            dirname(EAS_PLUGIN_BASENAME) . '/languages'
        );
        
        EAS_Frontend::init();
        EAS_Admin::init();
    }
}