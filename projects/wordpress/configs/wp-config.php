<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

/** 
 * MySQL settings -- we're looking up the environment variables that we pass to
 * containers (this is defined in our Kubernetes deployment manifest)
*/
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST'));


define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|@+TG-,)_f<YY3qX?=*v[5=Wdln^sYWQpo4Y]/X&GS,%=o-63R%S5^&9e{,g 9c-');
define('SECURE_AUTH_KEY',  'RpSGojgFNz<_q NoyX,^|]c]2xB<[Mg3}zUQ1.xqp(^fe1e-?nM?a&P@ZT_Lp{w]');
define('LOGGED_IN_KEY',    'G15$!^<_W2!=L,/bIi]I(]`L3w/vG.$vm`f]TkP-8A)bE-t`5fHvA2i;7Pb-?ZkX');
define('NONCE_KEY',        'C+-xO+*i$(^HR}3lc.5>n!goth{@Du.pf<>*e~nJ/blw?^}+JA#V-K`Zr*h}/+$}');
define('AUTH_SALT',        'Vlf/Wt|p)MeqQCkRe]8;)Z)SE]-:@HNRC3d>-;sA~uh+sQqW9zUHL3{.CSYC)xXK');
define('SECURE_AUTH_SALT', '/obx}7E3V rz86f^wrB!V;QHU@N!m9>m+.?CLDZs#FA,~|wR$52o$;-$^.26xQ#|');
define('LOGGED_IN_SALT',   '.T==PVQke|x}4I=Jx-*;QKw46-0X[_%&_T@DEs_aMjpK|Iqb^Ik369D(@7u9q^*[');
define('NONCE_SALT',       'MEc_c5S G(Z+.h<4;/p)5nsMwOMrC0WNs{(iqQbl+Qf|#!w$1)S,&K/N3cFg]c?c');

$table_prefix  = 'wp_';

define('WPLANG', '');
define('WP_DEBUG', false);

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

    /** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'AUTOMATIC_UPDATER_DISABLED', False );
define( 'WP_AUTO_UPDATE_CORE', minor );
