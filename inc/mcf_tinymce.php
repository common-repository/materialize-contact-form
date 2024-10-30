<?php

/**
 * Created by PhpStorm.
 * User: VanIllaSkyPE
 * Date: 15/06/2017
 * Time: 00:47
 */

if (!class_exists("mcf_tinymce")) {
    /**
     * Class mcf_constants
     */
    class mcf_tinymce
    {
		public function __construct()
		{
			add_action('admin_head', array($this, 'registerPlugin'));
		}


		public function registerPlugin()
		{
			// récupère la variable de contexte du type de post
			global $typenow;

			// on active le plugin pour les articles et les pages
			if (! in_array($typenow, array('post', 'page')))
				return ;

			// ce filtre permet d'ajouter du javascript arbitraire à l'éditeur de WP
			add_filter('mce_external_plugins', array($this, 'registerScript'));

			// On ajoute notre bouton à la première ligne de boutons
			add_filter('mce_buttons', array($this, 'registerButton'));
		}

	    /**
	     * inclut notre fichier javascript
	     * @param array $plugin_array
	     *
	     * @return array
	     */
		public function registerScript(array $plugin_array)
		{
			// notez ici que notre fichier s'appelle <strong>plugin.js</strong>
			// et est dans le même dossier que notre fichier .php
			// si vous changez les noms, pensez à modifier cette ligne
			$plugin_array['custom_button'] = plugins_url('/../public/js/tinymce.js', __FILE__);

			return $plugin_array;
		}

	    /**
	     * Ajoute l'id du bouton pour faire la correspondance avec le JS
	     * @param array $buttons
	     *
	     * @return array
	     */
		public function registerButton(array $buttons)
		{
			// nous passons ici un tableau contenant l'id du bouton
			// pour ajouter d'autres boutons, il suffit de passer les autres id
			array_push($buttons, 'mcf_button');

			return $buttons;
		}
    }
}
