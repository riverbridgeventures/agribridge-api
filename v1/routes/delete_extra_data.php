<?php 

	/**
	* 
	*/
	class Db_del_extra_data
	{
		private $conn;
		private $tablename = "";
	    private $tbl_arr = array();
		private $isDeleted;


		function __construct($tablename)
		{
			require_once dirname(__FILE__) . '/../../include/DbConnect.php';
            // opening db connection
            $db         = new DbConnect();
            $this->conn = $db->PDO();
            $this->tablename    = $tablename;
			
            $this->tbl_arr = array('tbl_farmers','tbl_applicant_knowledge','tbl_applicant_phone','tbl_asset_details','tbl_bank_loan_detail','tbl_cultivation_data','tbl_family_details','tbl_land_details','tbl_livestock_details','tbl_loan_details','tbl_residence_details','tbl_spouse_details','tbl_spouse_knowledge','tbl_yield_details');
		}

        public function isDeleted($id)
        {
            $table = $this->tablename;
            $res = $this->conn->query("SELECT * FROM ".$table. " WHERE id = " . $id." AND f_status = 0")->fetchAll();

            if(sizeof($res) > 0)
            {
                return false;
            }
            return true;
        }
		

		public function update($data)
        {
        	$resp_array         = array();
            $table              =  $this->tablename;

        	$udata['id']        = $data['server_id'];
            $udata['f_status']  = 1;

            $flag = $this->isDeleted($data['server_id']);// check previously deleted or not
            if(!$flag)
            {
                $query  =" UPDATE ".$table ;
                $query .=" SET ";
                $query  .= "f_status=:f_status ";
                $query  .=" WHERE  id=:id ";
             
                $stmt = $this->conn->prepare($query);
                $result = $stmt->execute($udata);
                if($result)
                {
                    return true;
                }else
                {
                    return flase;
                }
            }else
            {
                return true;
            }

        }

	}

	$app->post('/delete_extra_data', 'authenticate', function() use ($app){
        verifyRequiredParams(['tablename','server_id']); //provide a list of required parametes
        
        //declare variables
        $data = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        global $user_id;
        $tablename = $data['tablename'];

        // Start : check valid table or not
        $tbl_arr   = array('tbl_land_details', 'tbl_cultivation_data', 'tbl_yield_details', 'tbl_loan_details');
        if(!in_array($tablename,$tbl_arr))
        {
            $err_data = [
                ["error_code" => "404", "error_message" => "Invalid table name"]
            ];
        }
    	
    	//check if validation errors exists
        if($err_data !== []){
        	$response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else{

                $db          = new Db_del_extra_data($tablename);
                $return_data = $db->update($data);

                if ($return_data !== false) {
                $response["success"] = true;
                $response["data"] = ["message" => "Data deleted successfully!"];
                } else {
                    $response["success"] = false;
                    $response["data"] = [
                        ["error_code" => "103", "error_message" => "Data could not be deleted."]
                    ];
                    echoResponse(201, $response);
                }
                echoResponse(201, $response);
        }
    });