<?php
ob_start();
include("connection.php");


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $sn = $_REQUEST['sn'];
   
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE no_sn = :sn ");
       

    $stmt->bindParam(':sn', $sn);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
 


    if ($record ) {
      
        $response = array('success' => true, 'message' => 'Records found', 'data' => $record);
    } else {
        $response = array('success' => false, 'message' => 'No records found');
    }

    header('Content-Type: application/json');
    echo json_encode($response);

}


$pdo = null;
?>