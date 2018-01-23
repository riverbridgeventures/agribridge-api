<?php 

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
               
                $this->tbl_arr  = array('tbl_farmers','tbl_applicant_knowledge','tbl_applicant_phone','tbl_asset_details','tbl_bank_loan_detail','tbl_cultivation_data','tbl_family_details','tbl_land_details','tbl_livestock_details','tbl_loan_details','tbl_residence_details','tbl_spouse_details','tbl_spouse_knowledge','tbl_yield_details');

            }

            public function getall($fm_ids_arr,$limit)
            {
                $tables = $this->tbl_arr;

                $resp_array = array();
                $fm_ids     = array();

                if($limit=='')
                {
                    $limit = 1;
                }


                $placeHolders = implode(', ', array_fill(0, count($fm_ids_arr), '?'));

                    // Prepare the statement
                $STH =$this->conn->prepare("SELECT * FROM tbl_farmers WHERE fm_id NOT IN ($placeHolders) AND f_status=0 ORDER BY fm_id DESC LIMIT ".$limit." ");
                foreach ($fm_ids_arr as $index => $value) 
                {
                        $STH->bindValue($index + 1, $value, PDO::PARAM_INT);
                        
                }

                    // This should now work
                $result = $STH->execute();
                while($row = $STH->fetch(PDO::FETCH_ASSOC))
                {
                    array_push($fm_ids,$row['fm_id']);
                    
                }


                foreach($fm_ids as $fm_id)
                {
                    $table_array   = array();
                    foreach($tables as $table)
                    {
                        $statement = $this->conn->prepare("SELECT * FROM ".$table." WHERE fm_id =:id AND f_status=:status ");
                        $statement->execute(array(':id' => $fm_id,'status'=>0));
                        //$row = $statement->fetch(); 
                        while($row = $statement->fetch(PDO::FETCH_ASSOC))
                        {
                           $tarr = array('tablename'=>$table,'rows'=>$row);
                           array_push($table_array,$tarr);
                            
                        }
                    }

                    $resp = array('fm_id'=>$fm_id,'values:'=>$table_array);
                    array_push($resp_array,$resp);
                }// foreach fm_ids

                    
               
                return $resp_array;
            }
        }

        $app->post('/fm_data', 'authenticate', function() use ($app){
        verifyRequiredParams(['total', 'fm_ids']); //provide a list of required parametes
        
        //declare variables
        $data = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        global $user_id;
    	$total = $data['total'];

    	// Start : check valid table or not
    	
    	

    	$fm_ids_arr  = array();
    	foreach($data['fm_ids'] as $id){
            if($id !="")
            {
            	array_push($fm_ids_arr,$id);
            }
        }
       
		if(empty($fm_ids_arr))
    	{
    		$err_data = [
            	["error_code" => "404", "error_message" => "Farmer id's not found"]
            ];
    	}
    	
        if($err_data !== []){
        	$response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else
        {

        	$data['fm_caid'] = $user_id;
	        
            $db          = new Db_data_table();
            
            $return_data = $db->getAll($fm_ids_arr,$data['total']);
            
            if ($return_data !== false) {
	            $response["success"] = true;
	            $response["data"] = $return_data;
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