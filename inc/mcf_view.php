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

include_once plugin_dir_path(__FILE__) . '/../inc/mcf_constants.php';

if (!class_exists("mcf_view")) {
    /**
     * Class mcf_view
     */
    class mcf_view
    {
        /**
         * FUNCTION THAT SHOW THE HOME PLUGIN ADMIN PAGE
         */
        public function getAdminPluginHomepage()
        {
            echo '<h1>'.get_admin_page_title().'</h1>';
            echo '<p>' . __('Plugin settings', 'materialize-contact-form') . '</p>';
            ?>
            <form method="post" action="options.php">
                <?php
                if (function_exists('is_multisite') && is_multisite()) {
                    settings_fields(get_current_blog_id() . '_mcf_form_settings');
                } else {
                    settings_fields('mcf_form_settings');
                }
                ?>
                <table class="form-table">
                    <tr>
                        <th>
                            <label><?php _e('REcaptcha public key', 'materialize-contact-form') ?></label>
                        </th>
                        <td>
                            <input style="width: 400px; max-width: 100%;" type="text" name="mcf_recaptcha_apikey"
                                   placeholder="<?php __('REcaptcha public key', 'materialize-contact-form') ?>"
                                   value="<?php echo get_option("mcf_recaptcha_apikey") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label><?php _e('REcaptcha private key', 'materialize-contact-form') ?></label>
                        </th>
                        <td>
                            <input style="width: 400px; max-width: 100%;" type="text" name="mcf_recaptcha_secret"
                                   placeholder="<?php __('REcaptcha private key', 'materialize-contact-form') ?>"
                                   value="<?php echo get_option("mcf_recaptcha_secret") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label><?php _e('Sender email', 'materialize-contact-form') ?></label>
                        </th>
                        <td>
                            <input style="width: 400px; max-width: 100%;" type="text" name="mcf_sender_email"
                                   placeholder="<?php __('Sender email', 'materialize-contact-form') ?>"
                                   value="<?php echo get_option("mcf_sender_email") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label><?php _e('Recipient email', 'materialize-contact-form') ?></label>
                        </th>
                        <td>
                            <input style="width: 400px; max-width: 100%;" type="text" name="mcf_recipient_email"
                                   placeholder="<?php __('Recipient email', 'materialize-contact-form') ?>"
                                   value="<?php echo get_option("mcf_recipient_email") ?>"/>
                        </td>
                    </tr>
                </table>
                <?php submit_button() ?>
            </form>
            <?php
        }


        /**
         * @return string
         */
        public function getConfirmation()
        {
            return '
                    <div class="mcf_form_layout">
                        <div class="toast">
                            <p>
                                ' . __(
                'Thank you for your message,
                we will follow up as soon as possible!',
                'materialize-contact-form'
            ) . '
                            </p>
                        </div>
                    </div>
                ';
        }


	    /**
	     * @param array $datas
	     * @param bool $valid
	     * @param string $recaptcha
	     * @param bool $posted
	     * @param null $gdpr
	     *
	     * @return string
	     */
        public function getForm(array $datas, $valid = false, $recaptcha = "", $posted = false, $gdpr = null)
        {
            $toast                              = ($posted && !$valid && isset($datas["mcf_email"]["value"])) ?
                '<div class="toast toast-error">
                    <p>' . __('The form has not been filled in correctly!', 'materialize-contact-form') . '</p>
                </div>' : "";

            $captcha                            = ($recaptcha != "") ?
                '<div class="g-recaptcha" data-sitekey="' . $recaptcha . '"></div>' : "";

            $consent                            = (!is_null($gdpr)) ?
                '<div class="input-field">
                    <p>
                        <input type="checkbox" id="mcf_gdpr" name="mcf_gdpr" required />
                        <label for="mcf_gdpr">' . $gdpr . '</label>
                    </p>
                    <input type="hidden" id="mcf_gdpr_enabled" name="mcf_gdpr_enabled" value="' . uniqid() . '"/>
                </div>' : '';

            return '
                    <div class="mcf_form_layout">
                        ' . $toast . '
                        <form action="" method="post" class="full">
                            <div class="full">
                                <div class="half input-field">
                                    <i class="material-icons prefix">perm_identity</i>
                                    <input type="text" id="mcf_lastname" name="mcf_lastname" required 
                                    class="validate ' . $datas["mcf_lastname"]["status"] . '" 
                                    value="' . esc_attr($datas["mcf_lastname"]["value"]) . '" 
                                    maxlength="' . mcf_constants::LASTNAMEMAX . '" 
                                    minlength="' . mcf_constants::LASTNAMEMIN . '">
                                    <label for="mcf_lastname">' . __('Lastname', 'materialize-contact-form') . '</label>
                                </div>
                                <div class="half input-field">
                                    <i class="material-icons prefix">perm_identity</i>
                                    <input type="text" id="mcf_firstname" name="mcf_firstname" required 
                                    class="validate ' . $datas["mcf_firstname"]["status"] . '" 
                                    value="' . esc_attr($datas["mcf_firstname"]["value"]) . '" 
                                    maxlength="' . mcf_constants::FIRSTNAMEMAX . '" 
                                    minlength="' . mcf_constants::FIRSTNAMEMIN . '">
                                    <label for="mcf_firstname">'.__('Firstname', 'materialize-contact-form').'</label>
                                </div>
                            </div>
                            <div class="full">
                                <div class="input-field">
                                    <i class="material-icons prefix">email</i>
                                    <input type="email" name="mcf_email" id="mcf_email" required 
                                    class="validate ' . $datas["mcf_email"]["status"] . '" 
                                    value="' . esc_attr($datas["mcf_email"]["value"]) . '" 
                                    maxlength="' . mcf_constants::EMAILMAX . '" 
                                    minlength="' . mcf_constants::EMAILMIN . '">
                                    <label for="mcf_email">Email</label>
                                </div>
                                <div class="input-field">
                                    <i class="material-icons prefix">bookmark</i>
                                    <input type="text" name="mcf_subject" id="mcf_subject" 
                                    class="validate" value="' . esc_attr($datas["mcf_subject"]["value"]) . '" 
                                    maxlength="' . mcf_constants::SUBJECTMAX . '" 
                                    minlength="' . mcf_constants::SUBJECTMIN . '">
                                    <label for="mcf_subject">' . __('Subject', 'materialize-contact-form') . '</label>
                                </div>
                            </div>
                            <div class="full">
                                <div class="input-field">
                                    <i class="material-icons prefix">mode_edit</i>
                                    <textarea id="mcf_message" name="mcf_message" required 
                                    class="materialize-textarea validate ' . $datas["mcf_message"]["status"] . '" 
                                    maxlength="' . mcf_constants::MESSAGEMAX . '" 
                                    minlength="' . mcf_constants::MESSAGEMIN . '"
                                    >' . esc_textarea($datas["mcf_message"]["value"]) . '</textarea>
                                    <label for="mcf_message">Message</label>
                                </div>
                            </div>
                            <div class="full">
                                ' . $consent . '
                            </div>
                            <div class="full">
                                ' . $captcha . '
                            </div>
                            <div class="full">
                                <div class="input-field">
                                    <div class="right-align">
                                        <button class="btn waves-effect waves-light btn-large teal darken-1" 
                                        type="submit" name="action">' . __('Send', 'materialize-contact-form') . '
                                            <i class="material-icons right">send</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                ';
        }


        /**
         * @param array $datas
         * @return string
         */
        public function getEmail(array $datas)
        {
            return '
            <body>
		<style type="text/css">
                    p{
                        margin:10px 0;
                        padding:0;
                    }
                    table{
                        border-collapse:collapse;
                    }
                    h1,h2,h3,h4,h5,h6{
                        display:block;
                        margin:0;
                        padding:0;
                    }
                    img,a img{
                        border:0;
                        height:auto;
                        outline:none;
                        text-decoration:none;
                    }
                    body,#bodyTable,#bodyCell{
                        height:100%;
                        margin:0;
                        padding:0;
                        width:100%;
                    }
                    #outlook a{
                        padding:0;
                    }
                    img{
                        -ms-interpolation-mode:bicubic;
                    }
                    table{
                        mso-table-lspace:0;
                        mso-table-rspace:0;
                    }
                    .ReadMsgBody{
                        width:100%;
                    }
                    .ExternalClass{
                        width:100%;
                    }
                    p,a,li,td,blockquote{
                        mso-line-height-rule:exactly;
                    }
                    a[href^=tel],a[href^=sms]{
                        color:inherit;
                        cursor:default;
                        text-decoration:none;
                    }
                    p,a,li,td,body,table,blockquote{
                        -ms-text-size-adjust:100%;
                        -webkit-text-size-adjust:100%;
                    }
                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass td,
                    .ExternalClass div,
                    .ExternalClass span,
                    .ExternalClass font{
                        line-height:100%;
                    }
                    a[x-apple-data-detectors]{
                        color:inherit !important;
                        text-decoration:none !important;
                        font-size:inherit !important;
                        font-family:inherit !important;
                        font-weight:inherit !important;
                        line-height:inherit !important;
                    }
                    #bodyCell{
                        padding:50px 50px;
                    }
                    .templateContainer{
                        max-width:600px !important;
                        border:0;
                    }
                    a.mcnButton{
                        display:block;
                    }
                    .mcnTextContent{
                        word-break:break-word;
                    }
                    .mcnTextContent img{
                        height:auto !important;
                    }
                    .mcnDividerBlock{
                        table-layout:fixed !important;
                    }
                    /***** Make theme edits below if needed *****/
                    /* Page - Background Style */
                    body,#bodyTable{
                        background-color:#e9eaec;
                    }
                    /* Page - Heading 1 */
                    h1{
                        color:#202020;
                        font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif;
                        font-size:26px;
                        font-style:normal;
                        font-weight:bold;
                        line-height:125%;
                        letter-spacing:normal;
                    }
                    /* Page - Heading 2 */
                    h2{
                        color:#202020;
                        font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif;
                        font-size:22px;
                        font-style:normal;
                        font-weight:bold;
                        line-height:125%;
                        letter-spacing:normal;
                    }
                    /* Page - Heading 3 */
                    h3{
                        color:#202020;
                        font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif;
                        font-size:20px;
                        font-style:normal;
                        font-weight:bold;
                        line-height:125%;
                        letter-spacing:normal;
                    }
                    /* Page - Heading 4 */
                    h4{
                        color:#202020;
                        font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif;
                        font-size:18px;
                        font-style:normal;
                        font-weight:bold;
                        line-height:125%;
                        letter-spacing:normal;
                    }
                    /* Header - Header Style */
                    #templateHeader{
                        border-top:0;
                        border-bottom:0;
                        padding-top:0;
                        padding-bottom:20px;
                        text-align: center;
                    }
                    /* Body - Body Style */
                    #templateBody{
                        background-color:#FFFFFF;
                        border-top:0;
                        border: 1px solid #c1c1c1;
                        padding-top:0;
                        padding-bottom:0px;
                    }
                    /* Body -Body Text */
                    #templateBody .mcnTextContent,
                    #templateBody .mcnTextContent p{
                        color:#555555;
                        font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif;
                        font-size:14px;
                        line-height:150%;
                    }
                    /* Body - Body Link */
                    #templateBody .mcnTextContent a,
                    #templateBody .mcnTextContent p a{
                        color:#ff7f50;
                        font-weight:normal;
                        text-decoration:underline;
                    }
                    /* Footer - Footer Style */
                    #templateFooter{
                        background-color:#e9eaec;
                        border-top:0;
                        border-bottom:0;
                        padding-top:12px;
                        padding-bottom:12px;
                    }
                    /* Footer - Footer Text */
                    #templateFooter .mcnTextContent,
                    #templateFooter .mcnTextContent p{
                        color:#cccccc;
                        font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif;
                        font-size:12px;
                        line-height:150%;
                        text-align:center;
                    }
                    /* Footer - Footer Link */
                    #templateFooter .mcnTextContent a,
                    #templateFooter .mcnTextContent p a{
                        color:#cccccc;
                        font-weight:normal;
                        text-decoration:underline;
                    }
                    @media only screen and (min-width:768px){
                        .templateContainer{
                            width:600px !important;
                        }
                    }
                    @media only screen and (max-width: 480px){
                        body,table,td,p,a,li,blockquote{
                            -webkit-text-size-adjust:none !important;
                        }
                    }
                    @media only screen and (max-width: 480px){
                        body{
                            width:100% !important;
                            min-width:100% !important;
                        }
                    }
                    @media only screen and (max-width: 680px){
                        #bodyCell{
                            padding:20px 20px !important;
                        }
                    }
                    @media only screen and (max-width: 480px){
                        .mcnTextContentContainer{
                            max-width:100% !important;
                            width:100% !important;
                        }
                    }
                </style>
            <center>
                <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;background-color: #e9eaec;">
                    <tr>
                        <td align="center" valign="top" id="bodyCell" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 50px 50px;width: 100%;">
                            <!-- BEGIN TEMPLATE // -->
                            <!--[if gte mso 9]>
                            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                                <tr>
                                    <td align="center" valign="top" width="600" style="width:600px;">
                            <![endif]-->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;border: 0;max-width: 600px !important;">
                                <tr>
                                    <td valign="top" id="templateBody" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #FFFFFF;border-top: 0;border: 1px solid #c1c1c1;padding-top: 0;padding-bottom: 0px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                            <tbody class="mcnTextBlockOuter">
                                            <tr>
                                                <td valign="top" class="mcnTextBlockInner" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer">
                                                        <tbody>
                                                        <tr>
                                                            <td valign="top" style="padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;" class="mcnTextContent"><table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style=" display:block;min-width: 100%;border-collapse: collapse;width:100%;">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="color:#333333;padding-top: 20px;padding-bottom: 3px;"><strong>' . __('Name', 'materialize-contact-form') . '</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color:#555555;padding-top: 3px;padding-bottom: 20px;">'.ucfirst(
                    esc_attr($datas["mcf_firstname"])
                ).' '.ucfirst(esc_attr($datas["mcf_lastname"])).'</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:1px solid #dddddd; display:block;min-width: 100%;border-collapse: collapse;width:100%;">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td style="color:#333333;padding-top: 20px;padding-bottom: 3px;"><strong>' . __('Email', 'materialize-contact-form') . '</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="color:#555555;padding-top: 3px;padding-bottom: 20px;"><a href="mailto:' . esc_attr($datas["mcf_email"]) . '">' . esc_attr($datas["mcf_email"]) . '</a></td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:1px solid #dddddd; display:block;min-width: 100%;border-collapse: collapse;width:100%;">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td style="color:#333333;padding-top: 20px;padding-bottom: 3px;"><strong>' . __('Message', 'materialize-contact-form') . '</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="color:#555555;padding-top: 3px;padding-bottom: 20px;">' . esc_textarea($datas["mcf_message"]) . '</td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" id="templateFooter" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #e9eaec;border-top: 0;border-bottom: 0;padding-top: 12px;padding-bottom: 12px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                            <tbody class="mcnTextBlockOuter">
                                            <tr>
                                                <td valign="top" class="mcnTextBlockInner" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer">
                                                        <tbody>
                                                        <tr>
                                                            <td valign="top" class="mcnTextContent" style="padding-top: 9px;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #aaa;font-family: Helvetica;font-size: 12px;line-height: 150%;text-align: center;">

                                                                <!-- Footer content -->
                                                                ' . __('Sent from', 'materialize-contact-form') . ' <a href="' . home_url() . '" style="color:#bbbbbb;">' . get_bloginfo("name") . '</a>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!--[if gte mso 9]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->
                            <!-- // END TEMPLATE -->
                        </td>
                    </tr>
                </table>
            </center>
        ';
        }
    }
}