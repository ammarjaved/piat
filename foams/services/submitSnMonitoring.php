<?php
session_start();
ob_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_REQUEST['id'])) {
            $stmt = $pdo->prepare('SELECT count(*) FROM public.ad_service_qr WHERE no_sn = :sn');
            $stmt->bindParam(':sn', $_POST['sn_number']);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record && $record['count'] > 0) {
                $pdo = null;
                $_SESSION['message'] = 'SN no already exists';
                $_SESSION['alert'] = 'alert-danger';
                header('Location: ../index.php');
                exit();
            }
            $sql = "INSERT INTO sn_monitoring (ba, description, user_status, notifictn_type, sn_number, priority, csp_paid_date, aging_days, jenis_kerja, pic_vendor, type, remark, date_complete)
            VALUES (:ba, :description, :user_status, :notifictn_type, :sn_number, :priority, :csp_paid_date, :aging_days, :jenis_kerja, :pic_vendor, :type, :remark, :date_complete)";
            $stmt = $pdo->prepare($sql);
        } else {
            $sql = "UPDATE sn_monitoring SET
            ba = :ba,
            sn_number = :sn_number,
            description = :description,
            user_status = :user_status,
            notifictn_type = :notifictn_type,
            priority = :priority,
            csp_paid_date = :csp_paid_date,
            aging_days = :aging_days,
            jenis_kerja = :jenis_kerja,
            pic_vendor = :pic_vendor,
            type = :type,
            remark = :remark,
            date_complete = :date_complete
            WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $_REQUEST['id']);
        }

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

        $stmt->execute();

        $stmt = $pdo->prepare('SELECT id FROM public.ad_service_qr WHERE no_sn = :sn');
        $stmt->bindParam(':sn', $_POST['sn_number']);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!isset($_REQUEST['id'])) {
            $stmt = $pdo->prepare("INSERT INTO public.ad_service_qr (
                ba, jenis_sambungan, no_sn, sn_monitoring_id, status
            ) VALUES (:ba, :jenis_sambungan, :no_sn, :sn_id , :status)");
            $stat = 'new';
             $stmt->bindParam(':status',$stat);
        } else {
            $stmt = $pdo->prepare("UPDATE public.ad_service_qr SET
                jenis_sambungan = :jenis_sambungan,
                no_sn = :no_sn 
            WHERE ba = :ba AND sn_monitoring_id = :sn_id");
        }
        $stmt->bindParam(':ba', $_POST['ba']);
        $stmt->bindParam(':jenis_sambungan', $_POST['type']);
        $stmt->bindParam(':no_sn', $_POST['sn_number']);
        $stmt->bindParam(':sn_id', $record['id']);

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

<!-- Your HTML form here -->
