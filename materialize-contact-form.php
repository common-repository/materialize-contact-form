<?php

/**
Plugin name: Materialize contact form
Description: A simple materialize style contact form with google REcaptcha optionally
Version: 1.1.4
Author: Geoffrey Kratz
Author URI: https://vsweb.be
License: GPL2
Text Domain: materialize-contact-form
 */

include_once plugin_dir_path(__FILE__) . '/inc/mcf_widget.php';

if (!class_exists("MaterializeContactForm")) {
    /**
     * Class MaterializeContactForm
     */
    class MaterializeContactForm
    {
        /**
         * MaterializeContactForm constructor.
         */
        public function __construct()
        {
            add_action('widgets_init', function () {
                register_widget('mcf_widget');
            });
        }
    }

    add_action('init', 'localization_init');

    function localization_init()
    {
        // Localization
        load_plugin_textdomain(
        	'materialize-contact-form',
	        false,
	        'materialize-contact-form/languages'
        );
    }
}

new MaterializeContactForm();
