<?php
/**
 * Single Program Template
 * Single training program template
 * 
 * WordPress requires this file to be in the theme root directory
 * Actual template content is located in custom-templates/single-program/ folder
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load actual template content
require_once get_template_directory() . '/custom-templates/single-program/template.php';
