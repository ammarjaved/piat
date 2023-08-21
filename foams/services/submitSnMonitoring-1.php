<?php
session_start();
ob_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ba = $_POST['ba'];

        $jenis_sambungan = $_POST['jenis_sambungan'];
        $no_sn = $_POST['sn_no'];

        if ($jenis_sambungan == 'OH') {
            $piat = 'yes';
        } else {
            $piat = 'no';
        }

        $stmt = $pdo->prepare('SELECT count(*) FROM public.ad_service_qr WHERE no_sn = :sn');
        $stmt->bindParam(':sn', $no_sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record && $record['count'] > 0) {
            $pdo = null;
            $_SESSION['message'] = 'SN no already exists';
            $_SESSION['alert'] = 'alert-danger';
            header('Location: ../index.php');
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO public.ad_service_qr (
                ba, jenis_sambungan, no_sn
            ) VALUES (:ba, :jenis_sambungan, :no_sn)");

        $stmt->bindParam(':ba', $ba);
        $stmt->bindParam(':jenis_sambungan', $jenis_sambungan);
        $stmt->bindParam(':no_sn', $no_sn);

        $stmt->execute();

        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'inserted successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'inserted failed';
        $_SESSION['alert'] = 'alert-danger';
    }

    $pdo = null;
    header('Location: ../index.php');
    exit();
}
?>
