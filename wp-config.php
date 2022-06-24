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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'threeTest' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '03d!4cGuuvyrrmr ^k_n}JIhe}2^^#P?J#M+/ESq*Z&No.J.Zd:?h]>i>SjpS<B(' );
define( 'SECURE_AUTH_KEY',  ' Vr8kPkm6Oq570|(+(aN9o]:T}PQ4[Dt,v8Ucc5|+6q8P: 2o _PVSLjb.x#P]]Y' );
define( 'LOGGED_IN_KEY',    'X?Kj_Lz55*do:4Ody>F&>uW0MRQL4{Q^L@,g1S/,qy<#VPT&SThFloY`ZbU->A|#' );
define( 'NONCE_KEY',        '`K@_yM{P.4k-<)TywrPZyIUId2&@Sx:~:535>tEc%-4IxgT#iM>wh.^3(ZdxD)N%' );
define( 'AUTH_SALT',        '(5*,/_ys$/(wmo?zax/,5AN)~)xBMw/7Czs*[jzWn?A_Adu7QG5E{6g7iu64!E!X' );
define( 'SECURE_AUTH_SALT', 'wdBJ8co~w<u]&af}WL3(Jy=GNTVl`w~RN6A#auy`sZs8laKFRCYz5_E3/Wxlo@]R' );
define( 'LOGGED_IN_SALT',   'PWk$;m8+-7u?`J82iUi/RhO0Z~hfxmiq:,xXK8lvBCUt<x{@O`T[j?Q-(3Gdb?1j' );
define( 'NONCE_SALT',       '.llHQSllX|K66HnfAeh2g#0:i:3P$`@ebY)$Q.P,zGa9:G/^sVb,?>1!6%5-hFE`' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
