<?php
session_start();
ob_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) ) {
    try {
        $id = $_POST['id'];
        $remarks = $_POST['remarks'];


 
        $stmt = $pdo->prepare('UPDATE public.ad_service_qr SET remark = :remark WHERE id = :id');
        $stmt->bindParam( ':remark' , $remarks );
        $stmt->bindParam( ':id'     , $id      );
        $stmt->execute();

        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'Update Successfully';
    } catch (PDOException $e) {
        // echo  $e->getMessage();
        // exit();
        // session_start();
        $_SESSION['message'] = 'Request Failed';
        $_SESSION['alert'] = 'alert-danger';
    }

    $pdo = null;
    header('Location: ../index.php');
    exit();
}
