<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hisclinic_local');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
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
define('AUTH_KEY',         'syhfcgUVY?79zM~UfM4!86g@$l*l,7??+dVgb4pBr:oE>E`O|db1Cl]tj?BRCsNF');
define('SECURE_AUTH_KEY',  'fP!aR@#VxD7UIW{+#jFoCFGP|4(W!+=M:b(kfr&)8bXP65:_s=PbYN_A.OeHVDJN');
define('LOGGED_IN_KEY',    'c,~`TISJxf(Cd%~DJ!Bj;wXvwhW<%X6R@,dHHogGg`5[)UQN}SFM*!>nkvSM&5g(');
define('NONCE_KEY',        'y|o`v9}/$YY%)&##QC1~cg!ev%^yBlHE?e&VJA+|UrDd$,K&w.`Ed!P`4d~E4`1X');
define('AUTH_SALT',        'dVqj*_D| dJR?z!!|L)6Q^RV-rpI?{:yrS{Znp`M:$j|p<$(^kctpV_8Rd95y#RL');
define('SECURE_AUTH_SALT', '24.HIEBo=G3(|fZ2)u[I-X$jiZw!|PqiB.4I4)n9<y8iB+&^/iLa&IDM#gI}S,i!');
define('LOGGED_IN_SALT',   'Kz$7*Z&i_W;0o1 -R+3S6ew1:lF^0_@ZrotJCOlfMCxy^ sO89wgr>t^h,K4+LtN');
define('NONCE_SALT',       ')0=:dyO-i,:kBb9jv+x{BLS$IvK_$c@cuR~)J|)A4p:#f]zgc}0EOI$+]c--?*B0');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'hc_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
