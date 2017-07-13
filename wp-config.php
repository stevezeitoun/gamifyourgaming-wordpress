<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'wordpress');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'stevesteve');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '+Js(I_i?nT>&xt$[/x@t=UV)Vqm&lNO>+se]N5 7%Na+Rcd|k<-[%ECQ+(4^;|Qb');
define('SECURE_AUTH_KEY',  'f%fw3UXQ?@)|YvHfSePdu[n:*,{T[tJ@C RXdIv29tQEjH0[S!e%$Le$N<oFrj]j');
define('LOGGED_IN_KEY',    'Np:Vw;7{$hMXTA0pVYun#N{xDOPED:wl-:UJE&X5gOZ7aSyZs>W`-1! <>IBif|]');
define('NONCE_KEY',        ')b{0N#d6qrPl+Y[V$x]QQ0oQ|mz|M~~1&b)rE,2bVi~{e`a+]e`r1lQjU/5g=b 9');
define('AUTH_SALT',        'j9_tBhqi?;dzyU_SE+Q#MhFzq[u,_DcyYS6%@w+v[F?qF3p9HKJs|FjMW6eIyuxQ');
define('SECURE_AUTH_SALT', 'F|b}7N~`5J:~r_i]YPbKd0Rey,(8S`-r-6QU5-zhC24$9|`GT.jk`nJ0_LiKfZ>3');
define('LOGGED_IN_SALT',   'S==pu*?`g:$J4n7tlUSU}K-#Q;-+si|1Md:|2My|9VKP,3NQN3OoRX+f^%XIcUK,');
define('NONCE_SALT',       'T.5`@a!Oxf(ztroa:uWZ$s.B33#t7xM}T66S$zQlDVmO1m7D1`0fCtFjc9+|r,Co');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
