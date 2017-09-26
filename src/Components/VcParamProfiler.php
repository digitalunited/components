<?php namespace DigitalUnited\Components;

class VcParamProfiler
{
    protected static $startTime = null;

    public static function startTimer()
    {
        if (WP_ENV !== 'development') {
            return;
        }

        self::$startTime = microtime(true);
    }

    public static function stopTimer($componentName)
    {
        if (WP_ENV !== 'development') {
            return;
        }

        /*
         * Don't warn if is an ajax call
         */
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        /*
         * Only warn if components is slower than 0.5ms
         */
        $executionTime = (microtime(true) - self::$startTime) * 1000;
        if ($executionTime < 0.9) {
            return;
        }

        add_action('admin_notices', function() use ($componentName) {
            $message = '';
            $message .= '<b>';
            $message .= "$componentName is running slow. Check the Readme.md file in the components plugin for suggestions.";
            $message .= '</b>';

            echo "<div class=\"notice notice-error\"><p>$message</p></div>";
        });
    }
}
