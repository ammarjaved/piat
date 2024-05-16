<?php

ob_start();

include("connection.php");


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $ba = $_REQUEST['ba'];
   
        $stmt = $pdo->prepare("SELECT * FROM public.users WHERE station = :ba ");
       

    $stmt->bindParam(':ba', $ba);
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