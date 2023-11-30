<?php
session_start();
ob_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) ) {
    try {
        $id = $_POST['id'];
        $remarks = $_Post['remarks'];

        

        $stmt = $pdo->prepare('')

        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'inserted successfully';
    } catch (PDOException $e) {
        // echo  $e->getMessage();
        // exit();
        // session_start();
        $_SESSION['message'] = 'inserted failed';
        $_SESSION['alert'] = 'alert-danger';
    }

    $pdo = null;
    header('Location: ../index.php');
    exit();
}
