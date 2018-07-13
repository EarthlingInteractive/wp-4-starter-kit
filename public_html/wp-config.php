<?php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    $_SERVER['HTTPS'] = 'on';

define('DB_NAME', getenv('DBNAME'));
define('DB_USER', getenv('DBUSER'));
define('DB_PASSWORD', getenv('DBPASS'));
define('DB_HOST', getenv('DBHOST') . ':' . getenv('DBPORT'));

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('AUTH_KEY', getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
define('NONCE_KEY', getenv('NONCE_KEY'));
define('AUTH_SALT', getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
define('NONCE_SALT',  getenv('NONCE_SALT'));

$table_prefix  = getenv('WP_TABLE_PREFIX');

define('WP_DEBUG', false);

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');

define('DISALLOW_FILE_EDIT', true);
define( 'WP_AUTO_UPDATE_CORE' , false ); 
