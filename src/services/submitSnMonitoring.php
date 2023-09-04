<?php
session_start();
ob_start();
include 'connection.php';

// echo " asdasdasd";
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


            if ($_POST['jenis_sambungan'] == "OH") {
                $stat = "Inprocess";
                $piat = "yes";
            }else{
                $stat = "Complete";
                $piat = "no";
            }
       
            $sql = "INSERT INTO ad_service_qr (ba, alamat, user_status,piat , no_sn, jenis_sn, csp_paid_date, aging_days, jenis_sambungan, pic_vendor,  remark, status, created_by , tarikh_siap , qr)
            VALUES (:ba, :alamat, :user_status ,:piat,  :sn_number, :jenis_sn, :csp_paid_date, :aging_days, :jenis_sambungan, :pic_vendor,  :remark , :status, :created , :tarikh_siap , 'false')";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':created',$_SESSION['user_id']);
           
        } else {
            $sql = "UPDATE ad_service_qr SET
            ba = :ba,
            no_sn = :sn_number,
            alamat = :alamat,
            user_status = :user_status,
            jenis_sn = :jenis_sn,
            csp_paid_date = :csp_paid_date,
            aging_days = :aging_days,
            jenis_sambungan = :jenis_sambungan,
            pic_vendor = :pic_vendor,
            remark = :remark,
            piat = :piat,
            tarikh_siap =:tarikh_siap,
            status =:status
            WHERE id = :id";

            $stmt = $pdo->prepare($sql);
     

            $stmt->bindParam(':id', $_REQUEST['id']);
           
        }
        if ($_POST['jenis_sambungan'] == "OH") {
           
            $piat = "yes";
        }else{
           
            $piat = "no";
        }
        $tarik_siap = isset($_POST['complete_date']) ? $_POST['complete_date'] : '';
        $stmt->bindParam(':status',$_POST['cons_status']);
        $stmt->bindParam(':tarikh_siap' ,$tarik_siap );
        $stmt->bindParam(':ba', $_POST['ba']);
        $stmt->bindParam(':alamat', $_POST['alamat']);
        $stmt->bindParam(':user_status', $_POST['user_status']);
        $stmt->bindParam(':sn_number', $_POST['sn_number']);
        $stmt->bindParam(':jenis_sn', $_POST['jenis_sn']);
        $stmt->bindParam(':csp_paid_date', $_POST['csp_paid_date']);
        $stmt->bindParam(':aging_days', $_POST['aging_days']);//
        $stmt->bindParam(':jenis_sambungan', $_POST['jenis_sambungan']);
        $stmt->bindParam(':pic_vendor', $_POST['pic_vendor']);
        $stmt->bindParam(':remark', $_POST['remark']);
        $stmt->bindParam(':piat',$piat);
        
        

        $stmt->execute();

        if (isset($_REQUEST['id'])) {
            $stmt = $pdo->prepare("UPDATE public.inspection_checklist SET project_no = :sn WHERE ad_service_id = :id");
            $stmt->bindParam(':sn',$_POST['sn_number']);
            $stmt->bindParam(':id',$_REQUEST['id']);
            $stmt->execute();
        }

       
        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'inserted successfully';
    } catch (PDOException $e) {

        echo $e->getMessage();
        exit;
        $_SESSION['message'] = 'inserted failed';
        $_SESSION['alert'] = 'alert-danger';
    }

    $pdo = null;
    header('Location: ../index.php');
    exit();
}
?>

<!-- Your HTML form here -->
