<?php 

	/**
	* 
	*/
	class Db_dtable
	{
		private $conn;
		private $tablename = "";
		private $gen_insert_query;
		private $gen_data_array;
		private $gen_update_query;
		private $gen_update_data_array;
		private $tbl_arr = array();
		private $fm_id_arr  = array();
        private $isDeleted;


		function __construct()
		{
			require_once dirname(__FILE__) . '/../../include/DbConnect.php';
            // opening db connection
            $db         = new DbConnect();
            $this->conn = $db->PDO();
			
            $this->tbl_arr = array('tbl_farmers','tbl_applicant_knowledge','tbl_applicant_phone','tbl_asset_details','tbl_bank_loan_detail','tbl_cultivation_data','tbl_family_details','tbl_land_details','tbl_livestock_details','tbl_loan_details','tbl_residence_details','tbl_spouse_details','tbl_spouse_knowledge','tbl_yield_details');
		}

        public function isDeleted($fm_id)
        {
            $flag = true;
            $tbl_arr = $this->tbl_arr;
            foreach($tbl_arr as $tbl)
            {
                $res = $this->conn->query("SELECT * FROM ".$tbl. " WHERE fm_id = " . $fm_id." AND f_status = 0")->fetchAll();

                if(sizeof($res) > 0)
                {
                    $flag = false;
                }
            }
            
            return $flag;
        }
		

		public function update($fm_ids)
        {
        	$resp_array    = array();
        	foreach($fm_ids as $fm_id)
            {
                $i=0;
                $flag = $this->isDeleted($fm_id);// check previously deleted or not
                if(!$flag)
                {
                    $tbl_arr = $this->tbl_arr;

                    foreach($tbl_arr as $tbl)//for each 14 tables
                    {
                        $udata = array();
                        if($i==0)
                        {
                           $udata['f_status'] = 1;
                        }
                        else
                        {
                            $udata['f_status'] =1;
                        }
                        
                        $udata['fm_id']  =$fm_id;

                        $query  =" UPDATE ".$tbl ;
                        $query .=" SET ";
                        $query  .= "f_status=:f_status ,";
                        
                        $query   =chop($query,',');

                        $query  .=" WHERE fm_id=:fm_id ";
                     
                        $stmt = $this->conn->prepare($query);
                        $result = $stmt->execute($udata);
                        if($result)
                        {
                            array_push($resp_array,$fm_id);
                        }

                        $i++;
                    }
                }else
                {
                    array_push($resp_array,$fm_id);// if previously deleted
                }

            }
        	
        	return $resp_array;
        }

	}

	$app->post('/delete_data', 'authenticate', function() use ($app){
        verifyRequiredParams(['fm_id','fm_ids']); 
        
        //declare variables
        $data = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        global $user_id;
    
        //set default values here

        $data['fm_caid'] = $user_id;

        $fm_ids_arr  = array();


    	foreach($data['fm_ids'] as $id)// check fm_ids is set or blank
        {
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
    	
    	//check if validation errors exists
        if($err_data !== []){
        	$response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else{

                $db          = new Db_dtable();
                $return_data = $db->update($fm_ids_arr);

                $response["success"] = true;
                $response["data"] = $return_data;

                echoResponse(201, $response);
        }
    });