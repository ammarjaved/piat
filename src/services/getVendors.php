<?php

ob_start();

include("connection.php");


if ($_SERVER["REQUEST_METHOD"] == "GET") {

  
   
    $stmt = $pdo->prepare("SELECT vendor_name FROM public.tbl_vendor");
       

    $stmt->execute();
    $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
 


    if ($record ) {
      
        $response = array('success' => true, 'message' => 'Records found', 'data' => $record);
    } else {
        $response = array('success' => false, 'message' => 'No records found');
    }

    header('Content-Type: application/json');
    echo json_encode($response);

}
 
?>