<?php

/**
 * Plugin Name: Price Online Learning
 * Plugin URI: http://www.priceonlinelearning.com/
 * Description: Dynamic learning for pricing your products.
 * Version: 1.0
 * Author: Price Online Learning
 * Author URI: http://www.priceonlinelearning.com/
 * License: GPL2
 */


if (!defined('POL_PLUGIN_FILE')) {
    define('POL_PLUGIN_FILE', __FILE__);
}

if (!class_exists('POL', false)) {
    include_once dirname(POL_PLUGIN_FILE) . '/includes/POL.php';
}

POL::instance();