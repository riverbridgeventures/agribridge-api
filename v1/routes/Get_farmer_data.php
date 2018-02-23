<?php 
    ini_set('memory_limit', '-1');
        class Db_data_table
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
               
                $this->tbl_arr  = array('tbl_farmers','tbl_personal_detail','tbl_applicant_knowledge','tbl_applicant_phone','tbl_asset_details','tbl_cultivation_data','tbl_family_details','tbl_land_details','tbl_livestock_details','tbl_loan_details','tbl_residence_details','tbl_spouse_details','tbl_spouse_knowledge','tbl_yield_details','tbl_appliances_details','tbl_financial_details');

            }

            public function getall($fm_ids_arr,$data)
            {
                $tables = $this->tbl_arr;

               
                $limit      = $data['total'];

                $resp_array = array();
                $fm_ids     = array();

                if($limit=='')
                {
                    $limit = 10;
                }

                    // Prepare the statement
                    //AND fm_caid='".$data['fm_caid']."'
                if(!empty($fm_ids_arr))
                {
                     $placeHolders = implode(', ', array_fill(0, count($fm_ids_arr), '?'));
                     $STH =$this->conn->prepare("SELECT * FROM tbl_farmers WHERE f_status=0  AND fm_id NOT IN ($placeHolders) AND fm_caid='".$data['fm_caid']."' ORDER BY fm_id DESC LIMIT 700");
                    foreach ($fm_ids_arr as $index => $value) 
                    {
                        $STH->bindValue($index + 1, $value, PDO::PARAM_INT);
                    }
                }else
                {
                    $STH =$this->conn->prepare("SELECT * FROM tbl_farmers WHERE f_status=0  AND fm_caid='".$data['fm_caid']."' ORDER BY fm_id DESC LIMIT 700 ");
                }
               

                    // This should now work
                $result = $STH->execute();
                
                $excel_fm_ids  = array();
                
                while($row = $STH->fetch(PDO::FETCH_ASSOC))
                {
                    array_push($fm_ids,$row['fm_id']);
                    
                    if($row['insert_type']==1)
                    {
                        array_push($excel_fm_ids,$row['fm_id']);
                    }
                    
                }


                foreach($fm_ids as $fm_id)
                {
                    $table_array   = array();
                   
                    foreach($tables as $table)
                    {  
                        $row_arr       = array();
                        $statement = $this->conn->prepare("SELECT * FROM ".$table." WHERE fm_id =:id AND f_status=:status ");
                        $statement->execute(array(':id' => $fm_id,'status'=>0));
                        //$row = $statement->fetch(); 
                        while($row = $statement->fetch(PDO::FETCH_ASSOC))
                        {
                          array_push($row_arr,$row);
                        }

                        $tarr = array('tablename'=>$table,'rows'=>$row_arr);
                        array_push($table_array,$tarr);
                    }

                    $resp = array('fm_id'=>$fm_id,'values'=>$table_array);
                    array_push($resp_array,$resp);
                }// foreach fm_ids

                // array_push($resp_array,$excel_fm_ids);
              
                return $resp_array;
            }
            
            public function getErrors($fm_ids_arr,$data)
            {
                $error_array               = array();

                if(!empty($fm_ids_arr))
                {
                     $placeHolders = implode(', ', array_fill(0, count($fm_ids_arr), '?'));
                     $STH =$this->conn->prepare("SELECT * FROM tbl_farmers WHERE f_status=0 AND insert_type=1  AND fm_id NOT IN ($placeHolders) AND fm_caid='".$data['fm_caid']."' ORDER BY fm_id DESC LIMIT 700");
                    foreach ($fm_ids_arr as $index => $value) 
                    {
                        $STH->bindValue($index + 1, $value, PDO::PARAM_INT);
                    }
                }else
                {
                    $STH =$this->conn->prepare("SELECT * FROM tbl_farmers WHERE f_status=0 AND insert_type=1  AND fm_caid='".$data['fm_caid']."' ORDER BY fm_id DESC LIMIT 700 ");
                }

                $result = $STH->execute();

              
                while($row = $STH->fetch(PDO::FETCH_ASSOC))
                {
                    array_push($error_array,$row['fm_id']);
                }

                return $error_array;
            }
            
        }

        $app->post('/fm_data', 'authenticate', function() use ($app){
        verifyRequiredParams(['total']); //provide a list of required parametes
        
        //declare variables
        $data = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        global $user_id;
    	$total = $data['total'];

    	// Start : check valid table or not
    	
    	

    	$fm_ids_arr  = array();

        if(@$data['fm_ids']!="")
        {
            $req_ids = explode(',',$data['fm_ids']);
            foreach($req_ids as $id){
                if($id !="")
                {
                    array_push($fm_ids_arr,$id);
                }
            }
        }
        	
        if($err_data !== []){
        	$response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else
        {

        	$data['fm_caid'] = $user_id;
	        
            $db          = new Db_data_table();
            
            $return_data = $db->getAll($fm_ids_arr,$data);
            $errors = $db->getErrors($fm_ids_arr,$data);
            
            if ($return_data !== false) {
	            $response["success"] = true;
	            $response["data"] = $return_data;
	            $response["error_ids"] = $errors;
	        } else {
	            $response["success"] = false;
	            $response["data"] = [
	            	["error_code" => "103", "error_message" => "Data could not be update."]
	            ];
	            echoResponse(201, $response);
	        }
	        echoResponse(201, $response);
        }
    });

?>