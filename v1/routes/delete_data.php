<?php 

    /**
    * 
    */
    class Db_del_data
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
            
            $this->tbl_arr = array('tbl_farmers','tbl_applicant_knowledge','tbl_applicant_phone','tbl_asset_details','tbl_appliances_details','tbl_cultivation_data','tbl_family_details','tbl_land_details','tbl_livestock_details','tbl_loan_details','tbl_residence_details','tbl_spouse_details','tbl_spouse_knowledge','tbl_yield_details','tbl_financial_details','tbl_personal_detail');
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
        

        public function update($fm_id)
        {
            $resp_array    = array();
            
            $flag       = $this->isDeleted($fm_id);// check previously deleted or not
            $del_status = true;
            if(!$flag)
            { 
                $tbl_arr = $this->tbl_arr;
                
                foreach($tbl_arr as $tbl)//for each 14 tables
                {
                    $udata = array();
                    $udata['f_status'] =1;
                    
                    $udata['fm_id']  =$fm_id;

                    $query  =" UPDATE ".$tbl ;
                    $query .=" SET ";
                    $query  .= "f_status=:f_status ,";
                    
                    $query   =chop($query,',');

                    $query  .=" WHERE fm_id=:fm_id ";
                
                    $stmt = $this->conn->prepare($query);
                    $result = $stmt->execute($udata);
                    if(!$result)
                    {
                      $del_status = false;
                    }

                   
                }
                
                return $del_status;
                
            }else
            {
                return true;// if previously deleted
            }
            
        }// update function end

    }

    $app->post('/fm_delete', 'authenticate', function() use ($app){
        verifyRequiredParams(['fm_id']); 
        
        //declare variables
        $data     = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        $fm_id    = $data['fm_id'];
        global $user_id;
    
        if($fm_id=='')
        {
            $err_data = [
                ["error_code" => "404", "error_message" => "Farmer id not found"]
            ];
        }
        
        //check if validation errors exists
        if($err_data !== []){
            $response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else
        {
            $db          = new Db_del_data();
            $return_data = $db->update($fm_id);

            $response["success"] = $return_data;
            $response["data"] = '';

            echoResponse(201, $response);
        }

    });