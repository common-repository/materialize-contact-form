<?php

/**
 *
 *
 *  Materialize Template <http://wordpress.org>
 *
 *  By KRATZ Geoffrey AKA Jul6art AKA VanIllaSkype
 *  for VsWeb <https://vsweb.be>
 *
 *  https://vsweb.be
 *  admin@vsweb.be
 *
 *  Special thanks to Brynnlow
 *  for his contribution
 *
 *  It is free software; you can redistribute it and/or modify it under
 *  the terms of the GNU General Public License, either version 2
 *  of the License, or any later version.
 *
 *  For the full copyright and license information, please read the
 *  LICENSE.txt file that was distributed with this source code.
 *
 *  The flex one, in a flex world
 *
 *     __    __    ___            __    __    __   ____
 *     \ \  / /   / __|           \ \  /  \  / /  |  __\   __
 *      \ \/ / _  \__ \  _         \ \/ /\ \/ /   |  __|  |  _\
 *       \__/ (_) |___/ (_)         \__/  \__/    |  __/  |___/
 *
 *                    https://vsweb.be
 *
 */

include_once plugin_dir_path(__FILE__).'/../inc/mcf_view.php';
include_once plugin_dir_path(__FILE__) . '/../inc/mcf_tinymce.php';
include_once plugin_dir_path(__FILE__).'/../inc/mcf_constants.php';

if (!class_exists("mcf_widget")) {
    /**
     * Class mcf_widget
     */
    class mcf_widget extends WP_Widget
    {
        private $view;

        /**
         * MCFWidget constructor.
         */
        public function __construct()
        {
            parent::__construct(
            	'mcf_widget',
	            'Materialize contact form',
	            array('description' => __('A new generation contact form', 'materialize-contact-form')
	            ));

            $this->view                 = new mcf_view();
	        $this->tinyMce              = new mcf_tinymce();
            add_shortcode('mcf_form', array($this, 'showForm'));
            if(is_admin()) {
                add_action('admin_menu', array($this, 'adminMenu'));
                add_action('admin_init', array($this, 'registerSettings'));
            }
        }


        /**
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {
            //...
            //doesn't really matter
            //your custom stuff
            //...
        }


        /**
         * @param $atts
         * @param $content
         * @return string
         */
        public function showForm($atts, $content)
        {
        	$gdpr = null;
        	if (is_array($atts)
	            && key_exists('consent', $atts)
	            && key_exists('gdpr', $atts)
	            && $atts['gdpr'] == 'true'
	            && strlen($atts['consent'])) {
		        $gdpr = $atts['consent'];
	        }

            //add plugin stylesheet and script
            wp_enqueue_style(
            	'materialize-contact-form-icons-google',
                '//fonts.googleapis.com/icon?family=Material+Icons',
                array(),
	            '1.0.0'
            );

            wp_enqueue_style(
            	'materialize-contact-form-front-style',
                plugins_url('materialize-contact-form') . '/public/css/materialize-contact-form.css',
                array(),
	            null,
	            null
            );

            wp_enqueue_script(
            	'materialize-contact-form-front-script',
                plugins_url('materialize-contact-form') . '/public/js/materialize-contact-form.js',
                array(),
	            null,
	            null);

            //validate form
            if (isset($_POST["mcf_email"])) {
                $formDatas              = $this->validateForm($_POST);
                $valid                  = $formDatas["valid"];
                $fields                 = $formDatas["fields"];
                $posted = true;
            } else {
                $valid                  = false;
                $fields                 = $this->getFieldsArray();
                $posted = false;
            }

            //print content
            $html                       = array($content);
            if($valid) {
                //form is post and valid
                //send email to admin
                if (function_exists('is_multisite') && is_multisite()) {
                    $sender                 = get_blog_option(
                    	get_current_blog_id(),
	                    'mcf_sender_email',
	                    'no-reply@example.com'
                    );

                    $recipient              = get_blog_option(
                    	get_current_blog_id(),
	                    'mcf_recipient_email',
	                    get_option('admin_email')
                    );
                } else {
                    $sender                 = get_option('mcf_sender_email', 'no-reply@example.com');
                    $recipient              = get_option('mcf_recipient_email', get_option('admin_email'));
                }

                wp_mail( $recipient, __(
                	'New message received',
	                'materialize-contact-form'),
	                $this->view->getEmail($_POST),
	                array('From: ' . $sender, 'Content-Type: text/html; charset=UTF-8'),
	                null
                );

                //show confirmation to user
                $html                       []= $this->view->getConfirmation();
            } else {
                //form is post and invalid or form is not post
                if (function_exists('is_multisite') && is_multisite()) {
                    $recaptcha              = get_blog_option(get_current_blog_id(), 'mcf_recaptcha_apikey', "");
                } else {
                    $recaptcha              = get_option('mcf_recaptcha_apikey', "");
                }
                if ($recaptcha != "") {
                    wp_enqueue_script(
                    	'materialize-contact-form-recaptcha-script',
                        'https://www.google.com/recaptcha/api.js',
                        array(),
	                    null,
	                    null
                    );
                }
                $html                       []= $this->view->getForm($fields, $valid, $recaptcha, $posted, $gdpr);
            }

            return implode('', $html);
        }


        /**
         * @param array $datas
         * @return array
         */
        public function validateForm(array $datas)
        {
            $email                      = sanitize_email($datas["mcf_email"]);
            $lastname                   = sanitize_text_field($datas["mcf_lastname"]);
            $firstname                  = sanitize_text_field($datas["mcf_firstname"]);
            $subject                    = sanitize_text_field($datas["mcf_subject"]);
            $message                    = sanitize_textarea_field($datas["mcf_message"]);

            $result                     = array (
                "fields"                    => array(
                    "mcf_lastname"              => array(
                        "value"                     => $lastname,
                        "status"                    => ($this->validateField(
							$lastname,
							mcf_constants::LASTNAMEMIN,
							mcf_constants::LASTNAMEMAX)) ? true : "invalid"
                    ),
                    "mcf_firstname"             => array(
                        "value"                     => $firstname,
                        "status"                    => ($this->validateField(
                        	$firstname,
	                        mcf_constants::FIRSTNAMEMIN,
	                        mcf_constants::FIRSTNAMEMAX)) ? true : "invalid"
                    ),
                    "mcf_email"                 => array(
                        "value"                     => $email,
                        "status"                    => ($this->validateField(
                        	$email,
	                        mcf_constants::EMAILMIN,
	                        mcf_constants::EMAILMAX)) ? true : "invalid"
                    ),
                    "mcf_subject"               => array(
                        "value"                     => $subject,
                        "status"                    => ($this->validateField(
                        	$subject,
	                        mcf_constants::SUBJECTMIN,
	                        mcf_constants::SUBJECTMAX)) ? true : "invalid"
                    ),
                    "mcf_message"               => array(
                        "value"                     => $message,
                        "status"                    => ($this->validateField(
                        	$message,
	                        mcf_constants::MESSAGEMIN,
	                        mcf_constants::MESSAGEMAX)) ? true : "invalid"
                    )
                )
            );

            $valid                      = true;

            if (isset($datas["g-recaptcha-response"])) {
                $captcha                = stripslashes( esc_html( $_POST["g-recaptcha-response"] ) );
	            if (function_exists('is_multisite') && is_multisite()) {
		            $privatekey              = get_blog_option(get_current_blog_id(), 'mcf_recaptcha_secret', "");
	            } else {
		            $privatekey              = get_option('mcf_recaptcha_secret', "");
	            }
                $valid                  = $this->validateRecaptcha($captcha, $privatekey);
            }

            if (isset($datas["mcf_gdpr_enabled"]) && (!isset($datas["mcf_gdpr"]) || $datas["mcf_gdpr"] !== "on")) {
                $valid                  = false;
            }

            if($valid) {
                $valid                  = (($result["fields"]["mcf_lastname"]["status"] === true)
                                           && ($result["fields"]["mcf_firstname"]["status"] === true)
                                           && ($result["fields"]["mcf_email"]["status"] === true)
                                           && ($result["fields"]["mcf_subject"]["status"] === true)
                                           && ($result["fields"]["mcf_message"]["status"] === true)) ?

	                true : false ;
            }

            $result["valid"]            = $valid;

            return $result;
        }


        /**
         * @param string $value
         * @param int $minLength
         * @param int $maxLength
         * @return bool
         */
        private function validateField($value = "", $minLength = 0, $maxLength = 0)
        {
            $length                     = strlen($value);
            $valid                      = true;
            if($minLength > 0 && $length < $minLength) {
                $valid                  = false;
            }
            if($maxLength > 0 && $length > $maxLength) {
                $valid                  = false;
            }
            return $valid;
        }


        /**
         * @param string $value
         * @return bool
         */
        private function validateRecaptcha($value = "", $privatekey)
        {
            $remote_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP );

	        $args = array(
		        'body' => array(
			        'secret'   => $privatekey,
			        'response' => $value,
			        'remoteip' => $remote_ip,
		        ),
		        'sslverify' => false
	        );
	        $resp = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $args );
            $result = json_decode( wp_remote_retrieve_body( $resp ), true );
            return array_key_exists('success', $result) && $result['success'] == true ? true : false;
        }


        /**
         * ADD THE PLUGIN TO THE ADMIN MENU
         */
        public function adminMenu()
        {
            add_menu_page(
                "Materialize contact form",
                "MCF Form",
                "manage_options",
                "mcf_form",
                array(
                    $this->view,
                    "getAdminPluginHomepage"
                ),
                "dashicons-forms",
                25
            );
        }


        /**
         * REGISTER THE PLUGIN SETTINGS
         */
        public function registerSettings()
        {
            if (function_exists('is_multisite') && is_multisite()) {
                register_setting(get_current_blog_id() . '_mcf_form_settings', 'mcf_recaptcha_apikey');
                register_setting(get_current_blog_id() . '_mcf_form_settings', 'mcf_recaptcha_secret');
                register_setting(get_current_blog_id() . '_mcf_form_settings', 'mcf_recipient_email');
                register_setting(get_current_blog_id() . '_mcf_form_settings', 'mcf_sender_email');
            }else{
                register_setting('mcf_form_settings', 'mcf_recaptcha_apikey');
                register_setting('mcf_form_settings', 'mcf_recaptcha_secret');
                register_setting('mcf_form_settings', 'mcf_recipient_email');
                register_setting('mcf_form_settings', 'mcf_sender_email');
            }
        }


        /**
         * @return array
         */
        public function getFieldsArray()
        {
            return array(
                "mcf_lastname"              => array(
                    "value"                     => "",
                    "status"                    => ""
                ),
                "mcf_firstname"             => array(
                    "value"                     => "",
                    "status"                    => ""
                ),
                "mcf_email"                 => array(
                    "value"                     => "",
                    "status"                    => ""
                ),
                "mcf_subject"               => array(
                    "value"                     => "",
                    "status"                    => ""
                ),
                "mcf_message"               => array(
                    "value"                     => "",
                    "status"                    => ""
                )
            );
        }
    }
}