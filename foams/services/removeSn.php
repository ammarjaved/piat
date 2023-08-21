<?php
session_start();
ob_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $sn = $_REQUEST['sn'];

 
    try{

        $stmt = $pdo->prepare("DELETE  FROM public.ad_service_qr WHERE no_sn = :sn ");

        $stmt->bindParam(':sn', $sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("DELETE  FROM public.sn_monitoring WHERE sn_number = :sn ");

        $stmt->bindParam(':sn', $sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("DELETE FROM public.inspection_checklist WHERE project_no = :sn ");

        $stmt->bindParam(':sn', $sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
 
        $pdo = null;
        
        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'Remove successfully'; 
        
    } catch (PDOException $e) {


        // $_SESSION['message'] = ' failed'; 
        // $_SESSION['alert'] = 'alert-danger';
    
    }
   

    header("location:../index.php");
}



?>