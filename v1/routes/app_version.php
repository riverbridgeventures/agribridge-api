<?php 

        class Db_version
        {
            private $conn;
            private $tablename        = "";
            private $fm_ids_arr       = array();
            private $tbl_arr          = array();
            
          
            

            function __construct()
            {
                require_once dirname(__FILE__) . '/../../include/DbConnect.php';
                // opening db connection
                $db             = new DbConnect();
                $this->conn     = $db->PDO();
            }

            public function getVersion($req_version)
            {
                $stmt = $this->conn->prepare("SELECT * FROM tbl_app_version ORDER BY id DESC LIMIT 1 "); 
                $stmt->execute(); 
                $row = $stmt->fetch();
                $version  = $row['version'] ;

                if($version > $req_version)
                {
                    $res_arr = array('desc'=>$row['description'],'url'=>$row['url']);
                    return $res_arr;
                }else
                {
                    return false;
                }
            }
        }

        $app->post('/app_version', function() use ($app){
        verifyRequiredParams(['version']); //provide a list of required parametes
        
        //declare variables
        $data = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        global $user_id;

        $req_version =  $data['version'];
        
        if($err_data !== []){
            $response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else
        {
            $db          = new Db_version();
            
            $return_data = $db->getVersion($req_version);
            
            if ($return_data !== false) {
                $response["success"] = true;
                $response["data"] =[$return_data];
            } else {
                $response["success"] = false;
                $response["data"] =$return_data;
                echoResponse(201, $response);exit();
            }
            echoResponse(201, $response);
        }
    });

?>