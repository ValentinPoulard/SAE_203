<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'user' );

/** Database password */
define( 'DB_PASSWORD', 'bonjour' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'JqQw(jf.lE2f=`5Et;c!<~$Ue (Ojx:GC]S?|j&z_%r=Zzl=XT8ZI[EFo?g@]|dG' );
define( 'SECURE_AUTH_KEY',  'O>Z>-C+BXURx{ ^3=j8K:H_:/8)/+U-_+tUKu~Hqxl3jD,wX&2et!hCpY*8/W#2A' );
define( 'LOGGED_IN_KEY',    'U9T?QC;vrXM-?`m#V<cH%bFu[jkKkPL0QW:G;2t-DKR?eFu5$o)n||l]I--A0*/b' );
define( 'NONCE_KEY',        'j2-uGTj^C_;aw<YO8N|A.G! /G2$@Ha?_|3%-]zm4%t6b6p8?-.]3R]AVHnq^ueG' );
define( 'AUTH_SALT',        'h4n7klp[JSK;:XMj74k}bOPPfm-E6z&^iptJG%{.?t{u14LNE5qPfkuaqxVQ}c%$' );
define( 'SECURE_AUTH_SALT', 'YjODm85a:,4PoUuR5lrY(b<0FSP=Gminf6gM#{yrHk%8zm.kn{mlsFrClcUoH?[,' );
define( 'LOGGED_IN_SALT',   'r|Mv5$4BkU03%_56Y-}$A<J2//2gmzrB+UM7n]b9}3-G.fw-m9;Pxj6TN0g,<H8}' );
define( 'NONCE_SALT',       ' t/,Dtx|R/k)(%nH{r]T0u29BXFlKqD|z#$O{TACRN8D;dG<EH_r}v|qnYk+~D<@' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
