<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();

    $id = $_REQUEST['id'];
 
    try{

        $stmt = $pdo->prepare("DELETE  FROM public.ad_service_qr WHERE id = :id ");

        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT id  FROM public.ad_service_qr WHERE ad_service_id = :id ");

        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
 
        $pdo = null;
        
        
        
    } catch (PDOException $e) {


        // $_SESSION['message'] = ' failed'; 
        // $_SESSION['alert'] = 'alert-danger';
    
    }
    $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'Remove successfully'; 

    header("location: ../index.php");
}



?>