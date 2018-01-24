<?php

    
// Note by Ejaz ==============================================
//     Reference : "http://blog.aimanbaharum.com/2015/07/05/slim-framework-crud/"
//     Just go throw All the code in the reference link and you will understand 
//     the core functionality easily
// End of Note by Ejaz =======================================

// sleep(5);

require_once '../include/DbHandler.php';
require_once '../include/Utils.php';
require '../vendor/autoload.php';

$app = new \Slim\Slim();
if(SLIM_DEBUG){
  $app->config('debug',true);
}

/** ============================================== CORS ======
    Remember to remove the following code in production mode.
    ==========================================================*/
	
	// Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN'])) {
	    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	    header('Access-Control-Allow-Credentials: true');
	    // header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
	        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

	    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
	        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	    exit(0);
	}
	
//============================================================


/**
* route test block
*/
$app->get('/', function () {
    echo "restfull api v1";
});
$app->get('/test/:name', function ($name) {
    echo "Hello, $name";
});


//Add routes files here
require './routes/Authentication.php';
require './routes/Farmers.php';
require './routes/Add_farmer.php';
require './routes/Send_table.php';
require './routes/Send_extra_table.php';
require './routes/Get_farmer_data.php';
require './routes/delete_data.php';
require './routes/delete_extra_data.php';
require './routes/test.php';
require './routes/app_version.php';

// Run Slim
$app->run();
