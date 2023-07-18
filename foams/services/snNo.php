<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $sn = $_REQUEST['sn'];
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
    if($id == ""){
        $stmt = $pdo->prepare("SELECT count(*) FROM public.ad_service_qr WHERE no_sn = :sn ");
    }else{
        $stmt = $pdo->prepare("SELECT count(*) FROM public.ad_service_qr WHERE no_sn = :sn AND id != :id");
        $stmt->bindParam(':id',$id);
    }
    $stmt->bindParam(':sn', $sn);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
 


    if ($record && $record['count'] > 0) {
        $count = $record['count'];
        $response = array('success' => true, 'message' => 'Records found', 'count' => $count);
    } else {
        $response = array('success' => false, 'message' => 'No records found');
    }

    header('Content-Type: application/json');
    echo json_encode($response);

}


$pdo = null;
?>