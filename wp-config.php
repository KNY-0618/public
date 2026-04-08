<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'hZc9K;LA1WH_p)5=gN_=T8%=+1,242ET,L,Suv6W,oOJ;HhU:R.f:rb,C.#eLR,&' );
define( 'SECURE_AUTH_KEY',   'P#3&ujF$J)D=<q*Q#y7Ow39y20ir7Ye.nqOW?t)dKl3yFUx3p96DVS#oe@31JaR6' );
define( 'LOGGED_IN_KEY',     '*`vg;]?wwxT!cLk@xhHkxNrHbwB+%uJSgzN WnjqjmD+0AaUC%$A%R}oChCEsc;_' );
define( 'NONCE_KEY',         'BT _yF[{tE1nAG@@-Eg4NYBKn}W.Sz#~Sxp/dyg!%iOFf_bQ]@ro2SHH7v=C8^Zn' );
define( 'AUTH_SALT',         'J#}k96#Lx@e$`udoraD/kr= kR#y&C15o}[qsmqV2vL$WfuQlL!M 47Ls2`::32Z' );
define( 'SECURE_AUTH_SALT',  'IgU,rr@8H/,%lNfhI:~IsOabZy%CfuxM<X9*&D%7&aE|(yDj5Vt{Sh@s[^kW.6(*' );
define( 'LOGGED_IN_SALT',    ':#|v40i!;]PY47e7J8*#`:(wJGmIMb3; Gw h7xD7XNzV41*9kUh.1>Vi;;h**<h' );
define( 'NONCE_SALT',        '3%l_)&K.#nJd$:zIcL:S]JMLXG!=PIkBU8KLN$Cb,bFD#=44`O5_ Lu?/qn3IZZQ' );
define( 'WP_CACHE_KEY_SALT', 'O5N!g#b{]?Fd<IjKCtv+xzZxnc%%Nlee0IdTA_|SrHUC^aIwKy2xY{,-1{J!O74.' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
