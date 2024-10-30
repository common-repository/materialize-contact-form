/**
 * Created by gkratz on 09-05-2018.
 */
jQuery(document).ready(function () {
    tinymce.PluginManager.add('custom_button', function(editor, url) {
        // ajoute un bouton à tinyMCE
        editor.addButton('mcf_button', {

            // texte par défaut du bouton
            // on peut mettre une icône,
            // mais il faudra que vous trouviez ça tout seul ;)
            text: '',
            icon: 'wp-menu-image dashicons-before dashicons-forms',
            onclick: function() {
                // On ouvre une fenêtre modale
                // qui permet à l'utilisateur d'entrer ses données
                // de manière interactive
                editor.windowManager.open( {
                // titre du popup
                    title: 'MCF Form GDPR consent',
                    width: '450',
                    height: '200',
                    body: [
                // vous vous en servirez donc pour récupérer le contenu
                        {
                            type: 'checkbox',
                            name: 'gdpr',
                            label: 'Add GDPR Consent'
                        },

                // un deuxième champ
                        {
                            type: 'textbox',
                            name: 'consent',
                            label: 'GDPR Consent text',
                            multiline: true
                        }],

                // l'action a effectuer lorsque l'utilisateur valide la modale
                    onsubmit: function(e) {
                // On insère le contenu à l'endroit du curseur
                        editor.insertContent('[mcf_form gdpr="' + e.data.gdpr + '" consent="' + e.data.consent + '"]');
                    }
                });

                // editor.insertContent('[mcf_form]');
            }
        });
    });
});