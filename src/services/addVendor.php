<?php
ob_start();
session_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
   
    $vendor = isset($_POST["vendor"]) && $_POST["vendor"] !== "" ? $_POST["vendor"] : null;

    if ($vendor) {
        
        try 
        {
 
            $stmt = $pdo->prepare("INSERT INTO public.tbl_vendor (vendor_name) VALUES (:vendor)");
            $stmt->bindParam(':vendor',$vendor);
        
            $stmt->execute();
        
            $_SESSION['alert'] = 'alert-success';
            $_SESSION['message'] = 'Vendor Added'; 

        } catch (PDOException $e) {
        
            $_SESSION['message'] = 'inserted failed'; 
            $_SESSION['alert'] = 'alert-danger';
        }

    }else{
        $_SESSION['message'] = 'inserted failed'; 
        $_SESSION['alert'] = 'alert-danger';
    }
    $pdo = null;
    header("Location: ../index.php");
    exit;
}
?>
