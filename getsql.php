 <?php
header('Content-type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");


$data      = $_POST['data'];
$user_id   = $_POST['user_id'];
$filename  = $user_id.'-'.date('dmyhis').'.sql';
$myfile = fopen($filename, "w") or die("Unable to open file!");
fwrite($myfile, $data);
fclose($myfile);
echo json_encode(array("success"=>true));
?> 