<?php 


/** Database config */
//define('DB_USERNAME', 'root');  
//define('DB_PASSWORD', '');
if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '192.168.0.19'){
	define('DB_USERNAME', 'root');  
	define('DB_PASSWORD', '');  
	define('DB_HOST', 'localhost');  
	define('DB_NAME', 'acrefin_agribridge_2017');
}else{
	define('DB_USERNAME', 'acrefin_agribrid');  
	define('DB_PASSWORD', 'agribridge@!2017');  
	define('DB_HOST', 'localhost');  
	define('DB_NAME', 'acrefin_agribridge_2017');
}


/** Debug modes */
define('PHP_DEBUG_MODE', true);  
define('SLIM_DEBUG', true);


