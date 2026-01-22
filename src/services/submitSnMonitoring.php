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
       
            $sql = "INSERT INTO ad_service_qr (ba, alamat, user_status,piat , no_sn, jenis_sn,permit_sn, csp_paid_date, aging_days, jenis_sambungan, pic,  remark, status, created_by , tarikh_siap , qr,erms_status,vendor,work_type)
            VALUES (:ba, :alamat, :user_status ,:piat,  :sn_number, :jenis_sn,:permit_sn, :csp_paid_date, :aging_days, :jenis_sambungan, :pic,  :remark , :status, :created , :tarikh_siap , 'false',:erms,:vendor,:work_type)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':created',$_SESSION['user_id']);
           
        } else {
            $sql = "UPDATE ad_service_qr SET
            ba = :ba,
            no_sn = :sn_number,
            alamat = :alamat,
            user_status = :user_status,
            jenis_sn = :jenis_sn,
            permit_sn = :permit_sn,
            csp_paid_date = :csp_paid_date,
            aging_days = :aging_days,
            jenis_sambungan = :jenis_sambungan,
            pic = :pic,
            vendor = :vendor,
            remark = :remark,
            piat = :piat,
            tarikh_siap =:tarikh_siap,
            status =:status,
            erms_status=:erms,
            work_type=:work_type
            WHERE id = :id";

            $stmt = $pdo->prepare($sql);
     

            $stmt->bindParam(':id', $_REQUEST['id']);
           
        }

   
        $piat = $_POST['jenis_sambungan'] == "OH" ? "yes" : "no";
        $erms = isset($_POST['erms']) ? "done" : "pending";
        $work_type = isset($_POST['work_type']) ? $_POST['work_type'] :'' ;

        $tarik_siap = isset($_POST['complete_date']) ? $_POST['complete_date'] : '';
        $stmt->bindParam(':status',$_POST['cons_status']);
        $stmt->bindParam(':tarikh_siap' ,$tarik_siap );
        $stmt->bindParam(':ba', $_POST['ba']);
        $stmt->bindParam(':alamat', $_POST['alamat']);
        $stmt->bindParam(':user_status', $_POST['user_status']);
        $stmt->bindParam(':sn_number', $_POST['sn_number']);
        $stmt->bindParam(':jenis_sn', $_POST['jenis_sn']);
          $stmt->bindParam(':permit_sn', $_POST['permit_sn']);
        $stmt->bindParam(':csp_paid_date', $_POST['csp_paid_date']);
        $stmt->bindParam(':aging_days', $_POST['aging_days']);//
        $stmt->bindParam(':jenis_sambungan', $_POST['jenis_sambungan']);
        $stmt->bindParam(':pic', $_POST['pic']);
        $stmt->bindParam(':remark', $_POST['remark']);
        $stmt->bindParam(':piat',$piat);
        $stmt->bindParam(':erms',$erms);
        $stmt->bindParam(':vendor', $_POST['vendor']);
        $stmt->bindParam(':work_type', $work_type);


        
        
         
        $stmt->execute();
		
		 

        if (isset($_REQUEST['id'])) {
            $stmt = $pdo->prepare("UPDATE public.inspection_checklist SET project_no = :sn WHERE ad_service_id = :id");
            $stmt->bindParam(':sn',$_POST['sn_number']);
            $stmt->bindParam(':id',$_REQUEST['id']);
            $stmt->execute();
        } else {
            // Insert into permit_records only for new records (not updates)
            try {
                $permit_hostname = '192.168.1.34';
                $permit_port = 5432;
                $permit_database = 'permit_tracking';
                $permit_username = 'postgres';
                $permit_password = 'Admin123';

                $permit_pdo = new PDO(
                    "pgsql:host=$permit_hostname;port=$permit_port;dbname=$permit_database",
                    $permit_username,
                    $permit_password,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );

                // Split BA by '-' and get the last part after trimming
                $ba_parts = explode('-', $_POST['ba']);
                $ba_value = trim($ba_parts[count($ba_parts) - 1]);
                
                $permit_sql = "INSERT INTO public.permit_records (type, ba, sn, status_permit) 
                              VALUES (:type, :ba, :sn, :status_permit)";
                $permit_stmt = $permit_pdo->prepare($permit_sql);
                
                $permit_stmt->bindParam(':type', $_POST['permit_sn']);
                $permit_stmt->bindParam(':ba', $ba_value);
                $permit_stmt->bindParam(':sn', $_POST['sn_number']);
                $status_permit = 'MIGRATE';
                $permit_stmt->bindParam(':status_permit', $status_permit);
                
                $permit_stmt->execute();
                $permit_pdo = null;
                
            } catch (PDOException $e) {
                // Log permit insert error but don't stop the main process
                error_log("Permit records insert failed: " . $e->getMessage());
            }
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