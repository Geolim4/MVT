<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB_MVT'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
// Some characters you may want to copy&paste:
// ’ « » “ ” …
// Use: <strong style="color:green">Texte</strong>',
// For add Color

$lang = array_merge($lang, array(
	'MVT'	=> 'MVT',
	'MVT_BINARY' => 'Binaire',
	'MVT_BREADCRUMB_ARROW'	=> '»',
	'MVT_LANG' => 'fr',
	'MVT_DATE_FORMAT' => 'd M Y, H:i',
	'MVT_CFG_PHP_SYNTAX' => 'Vérificateur de syntaxe PHP',
	'MVT_COMPARE' => 'Comparer',
	'MVT_COMPARE_SELECT' => 'Sélectionner le mode de comparaison',
	'MVT_COMPARE_MODE' => array(
		'side_by_side' => 'Côte à côte',
		'inline' => 'Inclus',
		'unified' => 'Modification unie',
		'raw' => 'Modification unie brute',
	),
	'MVT_COMPARE_SORT' => array(
		'Images' => array(
			'ext-png' => 'PNG',
			'ext-gif' => 'GIF',
			'ext-jpg' => 'JPEG',
			'ext-tiff' => 'TIFF',
		),
		'Texte' => array(
			'ext-html' => 'HTML',
			'ext-css' => 'CSS',
			'ext-js' => 'JS',
			'ext-php' => 'PHP',
			'ext-sql' => 'SQL',
		),
		'Divers' => array(
			'ext-svg' => 'SVG',
			'ext-json' => 'JSON',
			'ext-xml' => 'XML',
		),
	),
	'MVT_CREDIT_LINE' => 'Mod Validation Tool © 2013, 2014 phpBB-fr MOD Team',
	'MVT_DIR_DELETE_FAILED' => 'Impossible de supprimer le répertoire <strong>%s</strong>.',
	'MVT_DIR_DELETED' => 'Le répertoire a été supprimé.',
	'MVT_DISABLED' => 'Cette fonctionnalité est en lecture seule en mode démo.',
	'MVT_DRAG_BUTTON' => '░',
	'MVT_BASE64_DECODE' => 'Decoder code base64',
	'MVT_INVALID_BASE64' => 'Chaine base64 invalide!',
	'MVT_INSERT_FILENAME' => 'Insérer le nom du fichier',
	'MVT_FILE_EOL' => 'Fichier EOL: %s',
	'MVT_FILE_ENCODING' => 'Encodage du fichier: %s',
	'MVT_FILTER' => 'Filtrer',
	'MVT_FILTER_EXPLAIN' => 'Sélectionnez une ou plusieurs options pour appliquer des filtres précis :',
	'MVT_FILE_DELETE_FAILED' => 'Impossible de supprimer le fichier <strong>%s</strong>.',
	'MVT_FILE_DELETE_FAILED_PROTECTED' => 'Impossible de supprimer le fichier <strong>%s</strong> car celui-ci est un fichier protégé.',
	'MVT_FILE_DELETED' => 'Le fichier a été supprimé.',
	'MVT_EXIT_HANDLER' => 'Avertisseur d’abandon',
	'MVT_FILE_MISSING' => 'Fichier inexistant',
	'MVT_FILE_MODIFIED' => 'Fichier modifié',
	'MVT_FILE_UNMODIFIED' => 'Fichier non modifié',
	'MVT_FILESTATS' => 'Récupérer les statistiques du fichier',
	'MVT_FULLSCREEN' => 'Alterner plein écran',
	'MVT_FULLSCREEN_UNSUPPORTED' => 'Votre navigateur ne supporte pas le mode plein écran !',
	'MVT_GIT_REPOSITORY' => 'Dépôt GIT',
	'MVT_LOADING' => 'Chargement...',
	'MVT_NO_BOM' => 'No BOM',
	'MVT_NO_XML' => 'Aucun fichier XML valide trouvé!',
	'MVT_NO_MOD' => 'Le MOD spécifié n’existe pas dans le dossier <strong>/mods</strong>.',
	'MVT_NO_MODS' => 'Aucun MOD trouvé dans le dossier <strong>/mods</strong>.',
	'MVT_MOD_ALTERNATIVE' => 'Vous pouvez aussi extraire un MOD dans le dossier <strong>/mods</strong> ou glisser-déposer le fichier la page actuelle.',
	'MVT_MOD_DELETED' => 'Le MOD a été supprimé.',
	'MVT_MOD_DELETE_WARN' => 'Cela va supprimer le MOD du dossier <strong>/mods</strong>. Êtes-vous sûr de vouloir continuer?',
	'MVT_MOD_DELETE_FAILED' => 'Impossible de supprimer le dossier <strong>%s</strong>.',
	'MVT_MOD_FAILED' => 'La connexion à l’URL spécifié a échouée.',
	'MVT_MOD_VERSION' => 'Version du MOD',
	'MVT_MOD_VERSION_COMPARE' => 'Version du MOD à comparer',
	'MVT_MOD_ALREADY_PRESENT' => 'Il semble que le MOD que vous avez tenté d’importer existe déjà dans le dossier <strong>/mods</strong>.',
	'MVT_CFG_PHP_BINARY_PATH' => 'Chemin de l’exécutable PHP',
	'MVT_CFG_PHP_BINARY_PATH_EXPLAIN' => 'Entrez le chemin de l’interface de commande PHP.
		<br /><strong>Utilisateurs windows </strong>: <em>C:\chemin_php\php.exe</em>.
			<br />Vous pouvez également définir PHP en tant que <em>variable d’environnement</em>, <a href="http://windows.fyicenter.com/view.php?ID=60">détails</a>.
		<br /><strong>Utilisateurs Linux</strong>: <em>php</em>',
	'MVT_ADD_MOD' => 'Ajouter un MOD',
	'MVT_ADD_MOD_URL' => 'Entrez l’URL du fichier distant. Pour indiquer plusieurs URL différentes, entrez chacun d’elles sur une nouvelle ligne.',
	'MVT_LANGUAGE' => 'Langage',
	'MVT_VERSION' => 'Version',
	'MVT_CFG_TAB_STR_LEN' => 'Nombre de caractère maximum des onglets de MOD',
	'MVT_DIRECTION' => 'ltr',
	'MVT_DISABLED_PHPBIN' => '<strong class="error">Vérificateur de syntaxe php désactivé!</strong>',
	'MVT_IDENTICAL_FILES' => 'Les deux fichiers sont identiques.',
	'MVT_INVALID_PHPBIN' => '<strong class="error">Chemin de l’exécutable PHP invalide!</strong>',
	'MVT_INTERNAL' => '@Interne',
	'MVT_LIST_NUMBERS' => 'Cacher la liste décimale',
	'MVT_LIST_LINEBREAK' => 'Retour automatique à la ligne',
	'MVT_PURGE' => 'Purger le bloc-note',
	'MVT_PURGE_CONFIRM' => 'Êtes-vous sûr de vouloir vider le bloc-note?',
	'MVT_NO_FILE' => 'Aucun fichier trouvé!',
	'MVT_NO_FILE_TO_COMPARE' => 'Le fichier à comparer n’existe pas!',
	'MVT_EMPTY_MESSAGE' => 'Message vide!',
	'MVT_INTERNAL_EXPLAIN' => 'Joindre un message à l’équipe de validation',
	'MVT_TOOLS' => 'Outils',
	'MVT_SELECT_ALL' => 'Tout sélectionner',
	'MVT_CLOSE_WINDOW' => 'Fermer la fenêtre',
	'MVT_INFORMATION' => 'Information',
	'MVT_DESCRIBE' => 'Décrire la raison qui vous pousse à rapporter cette portion de code:',
	'MVT_CANCEL' => 'Annuler',
	'MVT_ADD_TO_REPORT' => 'Ajouter au rapport',
	'MVT_HOME'	=> 'Accueil',
	'MVT_FILE_BROWSER' => 'Explorateur de fichier',
	'MVT_OLD_BROWSER'  => 'Votre navigateur ne supporte pas la fonctionnalité glisser-déposer !',
	'MVT_OPEN_NOTEPAD' => 'Ouvrir le bloc-note de validation',
	'MVT_PHP_SYNTAX' => 'Vérification de syntaxe PHP',
	'MVT_OK' => 'Ok',
	'MVT_CONTINUE' => 'Continuer',
	'MVT_NOTEPAD_TITLE' => 'Éditeur de validation',
	'MVT_SETTINGS' => 'Paramètres',
	'MVT_SETTINGS_SAVED' => 'Paramètres sauvegardés.',
	'MVT_NEW_TAB' => 'Ouvrir les liens des MODs dans un nouvel onglet',
	'MVT_SELECT_ERROR' => 'Merci de sélectionner une portion de code',
	'MVT_SELECT_SOME_OPTIONS' => 'Sélectionner une/des options',
	'MVT_SCROLL_DOWN' => 'Descendre',
	'MVT_SCROLL_TOP' => 'Monter',
	'MVT_SCROLL_LEFT' => 'Défiler à gauche',
	'MVT_SCROLL_RIGHT' => 'Défiler à droite',
	'MVT_SUPPORTED_VERSIONS' => 'Versions de phpBB supportées',
	'MVT_TAG_NOTICE' => 'NOTICE',
	'MVT_TAG_WARNING' => 'AVERTISEMENT',
	'MVT_TAG_FAIL' => 'ECHEC',
	'MVT_TOGGLE' => 'Afficher ou cacher le menu latéral',
	'MVT_VERSION_ERROR' => 'Impossible de récupérer la version depuis le serveur, message d’erreur: %s',
	'MVT_LATEST_VERSION' => 'Dernière version: %1$s, <a href="%1$s">plus d’informations</a>.',
	'MVT_EXPAND_ALL' => 'Tout déplier',
	'MVT_COLLAPSE_ALL' => 'Tout replier',
	'MVT_SEARCH_ENGINE' => 'Rechercher sur %s',
	'MVT_CFG_SEARCH_ENGINE' => 'Nom du moteur de recherche',
	'MVT_CFG_SEARCH_ENGINE_URL' => 'Adresse du moteur de recherche',
	'MVT_CFG_SEARCH_ENGINE_URL_EXPLAIN' => 'Entrez l’adresse du moteur de recherche en utilisant la variable <em>%CODE%</em> qui seras remplacée par le code à rechercher',
	'MVT_CFG_SEARCH_ENGINE_IMG' => 'Nom du fichier image du moteur de recherche',
	'MVT_EXIT_ALERT' => 'Votre bloc-note de validation n’est pas vide, si vous quittez sans le sauvegarder, vous allez perdre toutes ces données!',
));
// Context menu
$lang = array_merge($lang, array(
	'MVT_CONTEXTMENU_MARK_AS' => 'Marquer comme',
	'MVT_CONTEXTMENU_MARK_AS_CHECKED' => 'Vérifié',
	'MVT_CONTEXTMENU_MARK_AS_PRIORITIZED' => 'Prioritaire',
	'MVT_CONTEXTMENU_MARK_AS_NEW' => 'Nouveau',
	'MVT_CONTEXTMENU_MARK_AS_SUSPICIOUS' => 'Suspect',
	'MVT_CONTEXTMENU_NEW_TAB' => 'Ouvrir dans une nouvelle fenêtre',
	'MVT_CONTEXTMENU_EDITOR' => 'Voir dans l’éditeur',
	'MVT_CONTEXTMENU_PLAIN_TEXT' => 'Voir texte brut',
	'MVT_CONTEXTMENU_DOWNLOAD' => 'Télécharger',
	'MVT_CONTEXTMENU_MORE' => 'Plus',
	'MVT_CONTEXTMENU_DELETE' => 'Supprimer',
	'MVT_CONTEXTMENU_STATS' => 'Voir les statistiques',
	'MVT_CONTEXTMENU_ENCODING' => 'Voir l’encodage',
));
//dropzone.js
$lang = array_merge($lang, array(
	'DICT_DEFAULT_MESSAGE' => 'Glisser les fichiers ici pour uploader',
	'DICT_FALLBACK_MESSAGE' => 'Votre navigateur ne supporte pas la fonctionnalité glisser-déposer !',
	'DICT_FALLBACK_TEXT' => 'Merci d’utiliser le formulaire de recours ci-dessous pour télécharger vos fichiers comme dans les anciens temps ...',
	'DICT_INVALID_FILETYPE' => 'Vous ne pouvez pas uploader de fichier de ce type.',
	'DICT_FILE_TOO_BIG' => 'Fichier trop gros ({{filesize}}MiB). Taille de fichier maximum: {{maxFilesize}}MiB.',
	'DICT_RESPONSE_ERROR' => 'Le serveur a renvoyé un code {{statusCode}}.',
	'DICT_CANCEL_UPLOAD' => 'Annuler l’upload',
	'DICT_CANCEL_UPLOAD_CONFIRMATION' => 'Êtes-vous sûr de vouloir annuler l’upload?',
	'DICT_REMOVE_FILE' => 'Supprimer le fichier',
	'DICT_MAX_FILES_EXCEEDED' => 'Vous ne pouvez plus ajouter de fichiers.',
));