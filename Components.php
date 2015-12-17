<?php
/**
Plugin Name: Components
Plugin URI: https://github.com/digitalunited/components
Author: Digital United
Author URI: http://digitalunited.io
*/

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

load_theme_textdomain('components', get_template_directory() . '/lang');
load_child_theme_textdomain('components', get_stylesheet_directory() . '/lang');

$autoloader = new \DigitalUnited\Components\Autoloader();
$autoloader->requireFiles();
$autoloader->registerComponents();
