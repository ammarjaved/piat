<?php
 session_start(); 
 ob_start();  
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $sql = "INSERT INTO sn_monitoring (ba, description, user_status, notifictn_type, sn_number, priority, csp_paid_date, aging_days, jenis_kerja, pic_vendor, type, remark, date_complete)
            VALUES (:ba, :description, :user_status, :notifictn_type, :sn_number, :priority, :csp_paid_date, :aging_days, :jenis_kerja, :pic_vendor, :type, :remark, :date_complete)";


    $stmt = $pdo->prepare($sql);


    $stmt->bindParam(':ba', $_POST['ba']);
    $stmt->bindParam(':description', $_POST['description']);
    $stmt->bindParam(':user_status', $_POST['user_status']);
    $stmt->bindParam(':notifictn_type', $_POST['notifictn_type']);
    $stmt->bindParam(':sn_number', $_POST['sn_number']);
    $stmt->bindParam(':priority', $_POST['priority']);
    $stmt->bindParam(':csp_paid_date', $_POST['csp_paid_date']);
    $stmt->bindParam(':aging_days', $_POST['aging_days']);
    $stmt->bindParam(':jenis_kerja', $_POST['jenis_kerja']);
    $stmt->bindParam(':pic_vendor', $_POST['pic_vendor']);
    $stmt->bindParam(':type', $_POST['type']);
    $stmt->bindParam(':remark', $_POST['remark']);
    $stmt->bindParam(':date_complete', $_POST['date_complete']);


    if ($stmt->execute()) {
        echo "Data inserted successfully!";
    } else {
        echo "Error inserting data.";
    }
}
?>

<!-- Your HTML form here -->
