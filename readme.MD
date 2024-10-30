=== Materialize contact form ===
Contributors: jul6art
Tags: materialize, contact, material design, recaptcha, multilanguage
Requires at least: 4.6
Tested up to: 6.6
Stable Tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An easy, simple and complete materialize style contact form.

== Description ==

An easy, simple and complete materialize style contact form.

You can optionnally provide a google recaptcha apikey

The plugin validates the datas.

Then, if the form is valid, send automatic HTML emails

== Installation ==

To use the plugin, configure it in the settings MCF FORM menu section

And to insert it, use the shortcode

[mcf_form]
<?php echo do_shortcode('[mcf_form]'); ?>

You can also specify a consent message to add in the form

[mcf_form gdpr="true" consent="the text right to the consent checkbox"]
<?php echo do_shortcode('[mcf_form gdpr="true" consent="the text right to the consent checkbox"]'); ?>

The settings are not required

The google recaptcha Apikey is optionnal


== Frequently Asked Questions ==



== Screenshots ==

1. assets/screenshot-1.png
2. Assets/screenshot-2.png


== Changelog ==

V.1.1.0

Add possibility to inject the form from the wysiwyg
Add possibility to add a required consent checkbox whit the text you want (see screenshot-2)

V.1.2.0

Create stable tag


== Upgrade Notice ==

Please use our ticketing system when submitting your logs.  Please do not post to the forums.