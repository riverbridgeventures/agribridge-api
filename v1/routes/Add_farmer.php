<?php

    class Db_add_farmer
    {
        private $conn;
        function __construct()
        {
            require_once dirname(__FILE__) . '/../../include/DbConnect.php';
            // opening db connection
            $db         = new DbConnect();
            $this->conn = $db->connect();
        }

        public function get($data)
        {
            $stmt = $this->conn->prepare("SELECT * FROM tbl_farmers WHERE fm_caid = ? AND fm_id = ?");
            $stmt->bind_param("ii", $data['fm_caid'], $data['fm_id']);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $ret_data = [];
                while ($row = $result->fetch_assoc()) 
                {
                    $ret_data[] = $row;
                }
                $stmt->close();
                return $ret_data;
            } 
            else 
            {
                return  NULL;
            }
        }

        public function create($data)
        {
            $data_array = ['fm_caid', 'fm_id', 'fm_fname', 'fm_mname', 'fm_lname', 'fm_aadhar', 'fm_mobileno', 'fm_loan', 'fm_amount', 'f_status', 'fm_createddt', 'fm_createdby', 'fm_modifieddt', 'fm_modifiedby'];
            foreach($data_array as $val){
                if(!isset($data[$val])){
                    $data[$val] = '';
                }
            }

            $query = "INSERT INTO `tbl_farmers` (`fm_caid`, `fm_id`, `fm_fname`, `fm_mname`, `fm_lname`, `fm_aadhar`, `fm_mobileno`, `fm_loan`, `fm_amount`, `f_status`, `fm_createddt`, `fm_createdby`, `fm_modifieddt`, `fm_modifiedby`) VALUES(?, (SELECT t.maximum+1 FROM (SELECT max(fm_id) as maximum FROM tbl_farmers) t) , ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?)";
            $query_param = "isssiississss";

            $stmt = $this->conn->prepare($query);
            //var_dump($stmt);
            //  var_dump($this->conn->error);
            // exit();

           $a = $stmt->bind_param($query_param, $data['fm_caid'], $data['fm_fname'], $data['fm_mname'], $data['fm_lname'], $data['fm_aadhar'], $data['fm_mobileno'], $data['fm_loan'], $data['fm_amount'], $data['f_status'], $data['fm_createddt'], $data['fm_createdby'], $data['fm_modifieddt'], $data['fm_modifiedby']);
          
            $result = $stmt->execute();
            $insert_id = $stmt->insert_id;
            $stmt->close();




            // Check for successful insertion
            if ($result) {

                $stmt1 = $this->conn->prepare("SELECT fm_id from tbl_farmers WHERE id = ? LIMIT 1");
                $stmt1->bind_param("i", $insert_id);
                $farmer_data = [];
                if ($stmt1->execute()) 
                {
                    $result1 = $stmt1->get_result();
                    while ($row = $result1->fetch_assoc()) 
                    {
                        $farmer_data = $row;
                    }
                    $stmt1->close();
                }
                else{
                    return false;
                }

                if($farmer_data !== []){
                    return $farmer_data['fm_id'];
                }
                else{
                    return false;
                }

            } else {
                // Failed to create Farmer
                return false;
            }
        }

        public function update($data)
        {
            $data_array = ['fm_caid', 'fm_id', 'fm_name', 'fm_mname', 'fm_lname', 'fm_aadhar', 'fm_mobileno', 'fm_loan', 'fm_amount', 'f_status', 'fm_createddt', 'fm_createdby', 'fm_modifieddt', 'fm_modifiedby'];
            foreach($data_array as $val){
                if(!isset($data[$val])){
                    $data[$val] = '';
                }
            }

            $query = "UPDATE `tbl_farmers` SET `fm_caid` = ?,  `fm_fname` = ?, `fm_mname` = ?, `fm_lname` = ?, `fm_aadhar` = ?, `fm_mobileno` = ?, `fm_loan` = ?, `fm_amount` = ?, `f_status` = ?, `fm_createddt` = ?, `fm_createdby` = ?, `fm_modifieddt` = ?, `fm_modifiedby` = ? WHERE `fm_id` = ?";
            $query_param = "isssiississssi";

            $stmt = $this->conn->prepare($query);
            // var_dump($stmt);
             // var_dump($this->conn->error);
            // exit();
            $stmt->bind_param($query_param, $data['fm_caid'],  $data['fm_fname'], $data['fm_mname'], $data['fm_lname'], $data['fm_aadhar'], $data['fm_mobileno'], $data['fm_loan'], $data['fm_amount'], $data['f_status'], $data['fm_createddt'], $data['fm_createdby'], $data['fm_modifieddt'], $data['fm_modifiedby'], $data['fm_id']);
            $result = $stmt->execute();
            $stmt->close();
            // Check for successful insertion
            if ($result) {
                // Farmer successfully inserted
                return true;
            } else {
                // Failed to create Farmer
                return false;
            } 
        }

        public function validAAdhar($data)
        {
            
            $stmt = $this->conn->prepare("SELECT * FROM tbl_farmers WHERE fm_aadhar = ?");
            $stmt->bind_param("i", $data['fm_aadhar']);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $ret_data = [];
                while ($row = $result->fetch_assoc()) 
                {
                    $ret_data[] = $row;
                }
                $stmt->close();

                if(sizeof($ret_data) == 0){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }

        public function validMobile($data)
        {

            $stmt = $this->conn->prepare("SELECT * FROM tbl_farmers WHERE fm_mobileno = ?");
            $stmt->bind_param("i", $data['fm_mobileno']);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $ret_data = [];
                while ($row = $result->fetch_assoc()) 
                {
                    $ret_data[] = $row;
                }
                $stmt->close();

                if(sizeof($ret_data) == 0){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }

        public function validAAdharUpdate($data)
        {   
            $stmt = $this->conn->prepare("SELECT * FROM tbl_farmers WHERE fm_aadhar = ? AND fm_id != ?");
            $stmt->bind_param("ii", $data['fm_aadhar'], $data['fm_id']);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $ret_data = [];
                while ($row = $result->fetch_assoc()) 
                {
                    $ret_data[] = $row;
                }
                $stmt->close();

                if(sizeof($ret_data) == 0){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }

        public function validMobileUpdate($data)
        {

            $stmt = $this->conn->prepare("SELECT * FROM tbl_farmers WHERE fm_mobileno = ? AND fm_id != ?");
            $stmt->bind_param("ii", $data['fm_mobileno'], $data['fm_id']);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $ret_data = [];
                while ($row = $result->fetch_assoc()) 
                {
                    $ret_data[] = $row;
                }
                $stmt->close();

                if(sizeof($ret_data) == 0){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
    }

    $app->post('/add_farmer', 'authenticate', function() use ($app){
        verifyRequiredParams(['fm_fname', 'fm_lname', 'fm_aadhar', 'fm_mobileno']); //provide a list of required parametes
        
        //declare variables
        $data = $app->request->post(); //fetching the post data into variable
        $err_data = [];
        global $user_id;
        
        //set default values here
        $data['fm_caid'] = $user_id;
        $db = new Db_add_farmer();

        //Do validations here
        if(!$db->validAAdhar($data)){
            $err_data[] = ["error_code" => "101", "error_message" => "Adhar no. already exist."];
        }
        if(!$db->validMobile($data)){
            $err_data[] = ["error_code" => "102", "error_message" => "Mobile no. already exist."];
        }

        //check if validation errors exists
        if($err_data !== []){
            $response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else{

            //valid data hence inserting into table

             $data['fm_createddt'] = date('Y-m-d h:i:s');
             $data['fm_createdby'] =$user_id;


            $return_data    = $db->create($data);

            if ($return_data !== false) {
                $response["success"] = true;
                $response["data"] = ["fm_id" => $return_data, "message" => "Data added successfully!"];
            } else {
                $response["success"] = false;
                $response["message"] = "Failed to Add data. Please try again";
            }
            echoResponse(201, $response);
        }
    });

    $app->put('/add_farmer', 'authenticate', function() use ($app){
        verifyRequiredParams(['fm_id', 'fm_fname', 'fm_lname', 'fm_aadhar', 'fm_mobileno']); //provide a list of required parametes
        
        //declare variables
        $data = $app->request->put(); //fetching the put data into variable
        $err_data = [];
        global $user_id;
        
        //set default values here
        $data['fm_caid'] = $user_id;
        $db = new Db_add_farmer();

        //Do validations here
        if(!$db->validAAdharUpdate($data)){
            $err_data[] = ["error_code" => "101", "error_message" => "Adhar no. already exist."];
        }
        if(!$db->validMobileUpdate($data)){
            $err_data[] = ["error_code" => "102", "error_message" => "Mobile no. already exist."];
        }

        //check if validation errors exists
        if($err_data !== []){
            $response["success"] = false;
            $response["data"] = $err_data;
            echoResponse(201, $response);
        }else{

            //valid data hence inserting into table
            $data['fm_modifieddt']  = date('Y-m-d h:i:s');
            $data['fm_modifiedby']  = $user_id;

            $return_data = $db->update($data);

            if ($return_data !== false) {
                $response["success"] = true;
                $response["data"] = ["fm_id" => $return_data, "message" => "Data added successfully!"];
            } else {
                $response["success"] = false;
                $response["message"] = "Failed to Add data. Please try again";
            }
            echoResponse(201, $response);
        }
    });

?>