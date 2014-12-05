<?php
//English language array...have fun :)

$lang = array(
	"global" => array(
	//General
	"noauth" => "Vous n'êtes pas autorisé à voir cette page.",
	"home" => "home",
	"file" => "fichier",
	"files" => "fichiers",
	"map" => "carte",
	"music" => "musique",
	"servers" => "serveurs",
	"schedule" => "calendrier",
	"tournaments" => "tournois",
	"sponsors" => "sponsors",
	"staff" => "staff",
	"shoutbox" => "shoutbox",
	"benchmarks" => "benchmarks",
	"tech_support" => "support technique",
	"restaurants" => "restaurants",
	"policy" => "charte",
	"users" => "utilisateurs",
	"benchmarks" => "benchmarks",
	"profile" => "profil",
	"logout" => "se déconnecter",
	"login" => "se connecter",
	"register" => "s'enregistrer",
	"logedout_message" => "you are not logged in &gt; login",
	"forgot" => "mot de passe oublié ?",
	"admin" => "admin",
	"administrator" => "administrateur",
	"sadministrator" => "super administrateur",
	"change_pw" => "changer son mot de passe",
	//Left hand module stuff
 		//cpanel	
	"cpanel" => "panneau de contrôle",
	"cp_cuser" => "pseudo client",
	"cp_cip" => "adresse ip client",
	"food" => "nourriture",
	"gr" => "game request",
	"prizes" => "prix",
	"going_for" => "venir pour",
	"reg_for" => "s'inscrire à",
	"open_play" => "jeu ouvert",
	"cp_lodge" => "logements locaux",
	"cp_register" => "besoin d'un compte ? enregistrez-vous ici",
	"cp_security" => "&nbsp;pour une meilleure sécurité, activez les javascript.<br />&nbsp;nécessite un cookie.<br />",
	"cp_other" => "autres liens",
	
	
	"admin_guides" => "guide admin",
	"register_account" => "s'enregistrer pour avoir un compte",
	"schedule_hour" => "emploi du temps de l'heure à venir",
	"view_all" => "tout voir",
	"bench_link" => "compétiotion de bench",
	"caffeine" => "caféine",
	"caffeine_log" => "caféine log",
	"marathon" => "le marathon",
	"polls" => "sondages",
	"announcements" => "annonces",
	//omfg were gonna need a giant error set :)
	),
	
	"admin_modules" => array(
	"title" => "module config",
	"add_notes" => "Cela vous permet de déplacer les modules vers le haut ou le bas ainsi que de les activer/désactiver. Sélecionnez simplement un module et cliquez sur le bouton pour le déplacer.<br>NOTE: Cela nécessite que le toggle correspondant (si besoin est) soit activé.",
	"enable" => "activer",
	"enabled" => "activé",
	"up" => " monter ",
	"down" => "descendre",
	"disable" => "désactiver",
	"disabled" => "désactivé",
	),
	
	"admin_benchmark_cheaters" => array(
	"plural" => "tricheur aux benchmarks",
	"singular" => "reset les tricheurs",
	"notes_update" => "reset un benchmarks si il vous semble suspect ou faux. Cette opération est irréversible, aussi soyez prudent.",
	"desc_userid" => "pseudo",
	"error_userid" => "Vous avez oublié de sélectionner le pseudo du tricheur.",
	"noauth" => "Vous n'êtes pas autorisé à voir cette page.",
	),

	"admin_benchmarks" => array(
	"plural" => "benchmarks",
	"singular" => "benchmark",
	"notes_add" => "Ajouter ou modifier le benchmarks d'un joueur. Gardez en tête que tous les benchmarks listés comme faisant partie du score global sont nécessaires pour établir le gagnant global.",
	"desc_name" => "nom du benchmark",
	"error_name" => "Vous avez oublié d'entrer un nom de benchmark!",
	"desc_abbreviation" => "abréviation du benchmark",
	"desc_composite" => "Cela fait partie du score global ?",
	"desc_deflate" => "pourcentage de déflation appliquée au score quand il est ajouté au score global.",
	),
	
	"admin_caffeine_cheaters" => array(
	"plural" => "tricheurs du mode caféine",
	"singular" => "éffacer les tricheurs",
	"notes_update" => "Effacez le compte d'un joueur du mode caféine si il est suspecté de tricher. C'est irréversible, donc soyez prudent.",
	"desc_userid" => "pseudo du tricheur",
	"error_userid" => "Vous avez oublié de choisir le pseudo du tricheur.",
	),

	"admin_caffeine_types" => array(
	"plural" => "types de caféines",
	"singular" => "type de caféine",
	"notes_add" => "Ajouter ou modifier les types de caféine à disposition des joueurs.",
	"desc_name" => "Nom du type de caféine",
	"error_name" => "Vous avez oublié d'entrer un nom au type de caféine !",
	),

	"admin_caffeine" => array(
	"plural" => "objets caféines",
	"singular" => "caféine",
	"notes_add" => "Ajouter ou modifier les objets caféines proposés aux joueurs.",
	"desc_name" => "Nom de l'objets caféine",
	"error_name" => "Vous avez oublié d'entrer un nom à l'objet caféine !",
	"desc_caffeine_permg" => "quantité de caféine par milligramme (jusqu'à 10 positions décimales)",
	"error_caffeine_permg" => "pour calculer le contenu de caféine, vous devez entrer la quantité de caféine par milligramme dans la substance",
	"desc_ttype" => "type de substance",
	"descother_ttype" => "Ajouter d'autres types",
	),
	
	//is this legal?
	"admin_deleteuser" => array(
	"plural" => "éffacer des utilisateurs",
	"singular" => "éffacer un utilisateur",
	"notes_update" => "Supprimer un utilisateur inclue toutes les informations qui lui sont associées. Il sera éffacé de tous les tournois et sera remplacé par un vide. Ce n'est pas réversible donc soyez prudent. Vous ne pouvez pas éffacer un super admin, vous devez le rétrograder au stade d'administrateur ou d'utilisateur normal pour pouvoir l'éffacer.",
	//tournament...how to implement?"notes_update" => "deletes a user.  this is not reversible, so be careful.  super administrators are not shown here, you must first demote a user to administrator or normal user status to delete them.",
	"desc_userid" => "pseudo",
	"error_userid" => "vous avez oublié de sélectionner un utilisateur.",
	),
	
	"admin_disp_scores" => array(
	"plural" => "scores donnés par les joueurs",
	"singular" => "",
	"teamname" => "nom de l'équipe",
	"id" => "id",
	),

	"admin_foodrun" => array(
	"plural" => "food runs",
	"singular" => "food run",
	"notes_del" => "this feature is currently toggled off and users are not allowed to add food runs.  if you wish to change its status, you can <a href=\"admin_toggle.php\">toggle it on</a>.",
	"desc_userid" => "posté par",
	"desc_datetime_leaving" => "heure de départ",
	"error_datetime_leaving" => "you forgot to enter a time of departure!",
	"desc_headline" => "destination",
	"error_headline" => "you didn't say where you're going!",
	),
	
	"admin_games" => array(
	"plural" => "jeux",
	"singular" => "jeu",
	"notes_add" => "Liste de tous les jeux disponibles lors d'un tournoi. Si vous souhaitez lancer un tournoi sur un jeu, vous devez le lister ici.",
	"desc_name" => "Nom du jeu",
	"error_name" => "Vous avez oublié d'entrer un nom de jeu !",
	"desc_current_version" => "Version actuelle",
	"desc_url_update" => "Url relative ou absolue permettant de téléchager le dernier patch du jeu",
	"desc_url_maps" => "Url relative ou absolue menant à un pack de cartes",
	),

	"admin_gamingrig" => array(
	"plural" => "détails de la config",
	"singular" => "détail de la config",
	"notes_mod" => "modifier la config d'un utilisateur.",
	"desc_ms_sharename" => "nom de l'ordinateur",
	"desc_ms_workgroup" => "nom du groupe de travail (réseau)",
	"desc_ftp_server" => "Possède un serveur ftp ?",
	"desc_comp_proc" => "Spécifications du cpu (nom et vitesse)",
	"desc_comp_mem" => "Spécifications de la mémoire (quantité en Mb et type)",
	"desc_comp_hdstorage" => "Spécifications du disque dur (quantité en Go et nombre de disques)",
	"desc_comp_gfx" => "Spécifications de la carte graphique (quantité de mémoire en Mb et chipset)",
	),
);











//Installer stuff...old format
$lang["install"]['install'] = 'installer ALP';
$lang["install"]['success'] = 'succès !';
$lang["install"]['failure'] = 'échec.';
$lang["install"]['on'] = 'on';
$lang["install"]['off'] = 'off';
$lang["install"]['nolongerrequired'] = 'n\'est plus requis, c\'est ok si activé.';
$lang["install"]['optional'] = 'optionel, mais recomandé.';
$lang["install"]['start'] = 'Début';
$lang["install"]['end'] = 'Fin';
$lang["install"]['errors'] = 'ERREURS';
$lang["install"]['warning'] = 'ATTENTION!';
$lang["install"]['and'] = 'et';

$lang["install"]['stepone'] = 'étape une sur cinq';
$lang["install"]['stepone_passed'] = '<strong>Test de BDD: </strong>Passé';
//$lang["install"]['stepone_description'] = 'edit the _config.php file located in the / directory of ALP.  edit the variables to describe your lan party.';
$lang["install"]['stepone_description'] = 'Configurer la BDD - Veuillez entrer les paramètres de votre serveur SQL ci-dessus.';
$lang["install"]['stepone_next'] = 'aler à l\'étape deux - validation de _config.php et php.ini';
$lang["install"]['stepone_repeat'] = 'changer la configuration de la BDD';

$lang["install"]['steptwo'] = 'étape deux sur cinq';
$lang["install"]['steptwo_varone'] = 'nom de la lan';
$lang["install"]['steptwo_varone_error'] = "Vous devez entrer un nom de lan.";
$lang["install"]['steptwo_vartwo'] = "Nom du groupe de jeu";
$lang["install"]['steptwo_vartwo_error'] = "Vous devez entrer un nom de groupe de jeu.";
$lang["install"]['steptwo_varthree'] = "Maximum de participants";
$lang["install"]['steptwo_varthree_error'] = "Le nombre de participants maximum doit être supérieur à zéro.";
$lang["install"]['steptwo_varfour'] = "Pseudo du super admin";
$lang["install"]['steptwo_varfour_error'] = "Vous devez spécifier un pseudo de super admin.";
$lang["install"]['steptwo_varfive'] = "Information de la connexion mysql";
$lang["install"]['steptwo_varfive_error'] = "Vos informations de connexion mysql sont érronées.";
$lang["install"]['steptwo_varsix'] = "php.ini variable (magic_quotes_gpc)";
$lang["install"]['steptwo_varsix_error'] = "Editez php.ini pour avoir magic_quotes_gpc d'activé. Si vous ne savez pas comment faire cela; consultez Google ou le forum technique.";
$lang["install"]['steptwo_varseven'] = "langue par défaut";
$lang["install"]['steptwo_varseven_error'] = "Vous avez oublié de choisir une langue par défaut, ou la langue spécifiée n'est pas inclus dans les fichiers ALP.";
$lang["install"]['steptwo_vareight'] = "php.ini variable (short_open_tag)";
$lang["install"]['steptwo_vareight_error'] = "Editez php.ini pour avoir short_open_tags d'activé. Si vous ne savez pas comment faire cela; consultez Google ou le forum technique.";
$lang["install"]['steptwo_varnine'] = "php.ini variable (register_globals)";
$lang["install"]['satellitenotes'] = "Autres notes (ALP satellites):";
$lang["install"]['satellitenotes_valone'] = "Le nom de domaine de votre serveur web doit être ALP. (ie: http://alp/ est l'adresse réglée par le DNS; pas windows WINS).";
$lang["install"]['satellitenotes_valtwo'] = "Votre php doit avoir le ftp et les sockets d'extensions sécurisés d'activés.<br />(Ignorez cela si vous n'avez pas prévu d'utiliser les ALP Satellites)";
$lang["install"]['steptwo_varten'] = "Dates de début et de fin";
$lang["install"]['steptwo_varten_error'] = "La date de fin de votre lan doit être après la date de commencement.";
$lang["install"]['steptwo_vareleven'] = "php.ini variable (error reporting)";
$lang["install"]['steptwo_vareleven_error'] = "Ce n'est pas la valeur par défaut (E_ALL & ~E_NOTICE) ou (2039).<br />&nbsp;&nbsp;&nbsp;si votre valeur est plus strict; ALP vous renverra des erreurs.";
$lang["install"]['steptwo_vartwelve'] = "base de données mysql";
$lang["install"]['steptwo_vartwelve_error'] = "Le nom de la base de données mysql ne doit pas exister actuellement.<br />&nbsp;&nbsp;&nbsp;Si vous continuez, et qu'il n'existe toujours pas à l'étape quatre, il <br />&nbsp;&nbsp;&nbsp;sera créé automatiquement.";
	
$lang["install"]['steptwo_next'] = "aller à l'étape trois - configuration de la structure de la table mysql";
$lang["install"]['steptwo_redo'] = "Faites les modifications nécessaires au fichier _config.php et actualisez la page.";

$lang["install"]["stepthree"] = "étape trois sur cinq";
$lang["install"]["stepthree_warning"] = "Continuer éffacera la totalités des tables existantes d'ALP. Si vous avez une version précédente d'ALP que vous souhaitez conserver, pensez à sauvegarder votre base de donnée. Ce script remplace les tables existantes par des tables vides. A cause des changements trop importants qui ont été réalisés depuis la version précédente, il n'y a pas de script d'upgrade. Désolé.";
$lang["install"]["stepthree_doublewarning"] = "VOUS AVEZ ÉTÉ AVERTIS !!!";
$lang["install"]["stepthree_tournamentmodetitle"] = "mode tournois seulement";
$lang["install"]["stepthree_tournamentmode"] = "le mode tournoi va automatiquement configurer la BDD d'ALP en prennant en compte votre intention d'utiliser ALP uniquement pour des tournois. Cela va automatiquement désactiver toutes les capacités non nécessaires. Cette possibilité pourra être réactivée plus tard. ALP tournois est pour les tournois de jeux vidéo, ALP sports est pour tous les autres types de tournois (football, nage, basketball, etc.)";
$lang["install"]["stepthree_next_choice1"] = "aller à l'étape quatre - Version complète - création de la structure de la table mysql";
$lang["install"]["stepthree_next_choice2"] = "aller à l'étape quatre - ALP en mode tournois seulement - création de la structure de la table mysql";
$lang["install"]["stepthree_next_choice3"] = "aller à l'étape quatre - ALP en mode tournois sportifs seulement - création de la structure de la table mysql";
	
$lang["install"]["stepfour"] = "etape quatre sur cinq";
$lang["install"]["stepfour_creatingdatabase"] = "création de la BDD ALP";
$lang["install"]["stepfour_newtable"] = "créer un nouveau tableau";
$lang["install"]["stepfour_defaultvalues"] = "insérez les valeur par défaut dans";
$lang["install"]["stepfour_success"] = "création de la structure de la table réussie";
$lang["install"]["stepfour_warning"] = "soyez sûr d'avoir supprimé le fichier install.php avant d'utiliser le script";
$lang["install"]["stepfour_next"] = "aller à l'étape cinq - enregistrer le compte super admin";
$lang["install"]["stepfour_redo"] = "une erreur innatendue a surgie de l'espace.  actualisez cette page pour essayer de nouveau.";
	
$lang["install"]['coffee']    = 'café';
$lang["install"]['softdrink'] = 'sodas';
$lang["install"]['tea']       = 'thé';
$lang["install"]['other']     = 'autres';
?>
