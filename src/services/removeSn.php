<?php
session_start();
ob_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_name'])) {
    

    $sn = $_REQUEST['sn'];

 
    try{

        // select all recored 
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE no_sn = :sn ");

        $stmt->bindParam(':sn', $sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
     
        // if recored exists
       
        if ($record) {
           
            // insert recored to remove_ad_service_qr for backup
            $stmt = $pdo->prepare("INSERT INTO public.remove_ad_service_qr ( ba,
                jenis_sn, jenis_sambungan, no_sn, alamat, tarikh_siap, piat, nama_feeder_pillar, nama_jalan,
               jumlah_span, talian_utama, bil_umbang, bil_black_box, bil_lvpt, bilangan_serbis, catatan,
               seksyen_dari, seksyen_ke, bil_saiz_tiang_a, bil_saiz_tiang_b, bil_saiz_tiang_c,
               bil_jenis_spun, bil_jenis_konkrit, bil_jenis_besi, bil_jenis_kayu, abc_span_a, abc_span_b,
               abc_span_c, abc_span_d, pvc_span_a, pvc_span_b, pvc_span_c, bare_span_a, bare_span_b, bare_span_c, status ,user_status,
               csp_paid_date,aging_days,pic_vendor,remark,complete_date,created_at,created_by, qr, piat_status,talian_utama_s
           ) VALUES ( :ba, :jenis_sn, :jenis_sambungan, :no_sn, :alamat, :tarikh_siap, :piat, :nama_feeder_pillar,
               :nama_jalan, :jumlah_span, :talian_utama, :bil_umbang, :bil_black_box, :bil_lvpt,
               :bilangan_serbis, :catatan, :seksyen_dari, :seksyen_ke, :bil_saiz_tiang_a, :bil_saiz_tiang_b,
               :bil_saiz_tiang_c, :bil_jenis_spun, :bil_jenis_konkrit, :bil_jenis_besi, :bil_jenis_kayu,
               :abc_span_a, :abc_span_b, :abc_span_c, :abc_span_d, :pvc_span_a, :pvc_span_b, :pvc_span_c,
               :bare_span_a, :bare_span_b, :bare_span_c, :status ,:user_status , :csp_paid_date,:aging_days, :pic_vendor ,:remark,:complete_date,:created_at ,:created_by, :qr, :piat_status,:talian_utama_s)");
                // bind params
                $stmt->bindParam(':piat_status', $record['piat_status']);
                // $stmt->bindParam(':remove_date', date('y-m-d'));
                $stmt->bindParam(':qr', $record['qr']);
                $stmt->bindParam(':created_by', $record['created_by']);
                $stmt->bindParam(':created_at', $record['created_at']);
                $stmt->bindParam(':complete_date', $record['complete_date']);
                $stmt->bindParam(':talian_utama_s',$record['talian_utama_s']);
                $stmt->bindParam(':user_status', $record['user_status']);
                $stmt->bindParam(':csp_paid_date', $record['csp_paid_date']);
                $stmt->bindParam(':aging_days', $record['aging_days']);//
                $stmt->bindParam(':pic_vendor', $record['pic_vendor']);
                $stmt->bindParam(':remark', $record['remark']);
                $stmt->bindParam(':ba',$record['ba']);
                $stmt->bindParam(':jenis_sn', $record['jenis_sn']);
                $stmt->bindParam(':jenis_sambungan', $record['jenis_sambungan']);
                $stmt->bindParam(':no_sn', $record['no_sn']);
                $stmt->bindParam(':alamat', $record['alamat']);
                $stmt->bindParam(':tarikh_siap', $record['tarikh_siap']);
                $stmt->bindParam(':piat', $record['piat']);
                $stmt->bindParam(':nama_feeder_pillar', $record['nama_feeder_pillar']);
                $stmt->bindParam(':nama_jalan', $record['nama_jalan']);
                $stmt->bindParam(':jumlah_span', $record['jumlah_span']);
                $stmt->bindParam(':talian_utama', $record['talian_utama']);
                $stmt->bindParam(':bil_umbang', $record['bil_umbang']);
                $stmt->bindParam(':bil_black_box', $record['bil_black_box']);
                $stmt->bindParam(':bil_lvpt', $record['bil_lvpt']);
                $stmt->bindParam(':bilangan_serbis', $record['bilangan_serbis']);
                $stmt->bindParam(':catatan', $record['catatan']);
                $stmt->bindParam(':seksyen_dari', $record['seksyen_dari']);
                $stmt->bindParam(':seksyen_ke', $record['seksyen_ke']);
                $stmt->bindParam(':bil_saiz_tiang_a', $record['bil_saiz_tiang_a']);
                $stmt->bindParam(':bil_saiz_tiang_b', $record['bil_saiz_tiang_b']);
                $stmt->bindParam(':bil_saiz_tiang_c', $record['bil_saiz_tiang_c']);
                $stmt->bindParam(':bil_jenis_spun', $record['bil_jenis_spun']);
                $stmt->bindParam(':bil_jenis_konkrit', $record['bil_jenis_konkrit']);
                $stmt->bindParam(':bil_jenis_besi', $record['bil_jenis_besi']);
                $stmt->bindParam(':bil_jenis_kayu', $record['bil_jenis_kayu']);
                $stmt->bindParam(':abc_span_a', $record['abc_span_a']);
                $stmt->bindParam(':abc_span_b', $record['abc_span_b']);
                $stmt->bindParam(':abc_span_c', $record['abc_span_c']);
                $stmt->bindParam(':abc_span_d', $record['abc_span_d']);
                $stmt->bindParam(':pvc_span_a', $record['pvc_span_a']);
                $stmt->bindParam(':pvc_span_b', $record['pvc_span_b']);
                $stmt->bindParam(':pvc_span_c', $record['pvc_span_c']);
                $stmt->bindParam(':bare_span_a', $record['bare_span_a']);
                $stmt->bindParam(':bare_span_b', $record['bare_span_b']);
                $stmt->bindParam(':bare_span_c', $record['bare_span_c']);
                $stmt->bindParam(':status', $record['status']);

                $stmt->execute();
                // echo "befoore";
                // exit;
                // echo "aftrer";
                
                // get checkliast recored
                $stmt = $pdo->prepare("SELECT *  FROM public.inspection_checklist WHERE project_no = :sn ");

                $stmt->bindParam(':sn', $sn);
                $stmt->execute();
                $record1 = $stmt->fetch(PDO::FETCH_ASSOC);

                // check if checklist recored is exists

                if ($record1) {

                    // insert recored into remove_inspection_checklist for backup

                    $stmt = $pdo->prepare("INSERT INTO public.remove_inspection_checklist
                    (piat_date, project_no, project_name, feeder_circuit, feeder_circuit_from, feeder_circuit_to,
                    feeder_circuit_length, voltage_level, cable_type, company, company_name, company_phone_no,
                    company_sign, inspection_checklist, result, attempt_no, prepare_by, prep_sign, ad_service_id, created_by)
                    VALUES
                    (:piatDate, :projectNo, :projectName, :feederCircuit, :feederCircuitFrom, :feederCircuitTo,
                    :feederCircuitLength, :voltageLevel, :cableType, :company, :companyName, :companyPhoneNo,
                    :companySign, :inspectionChecklist, :result, :attemptNo, :prepareBy, :prepSign , :ad_service_id, :created)");
                    $stmt->bindParam(':ad_service_id',$record1['']);
                    $stmt->bindParam(':created',$record1['user_id']);  
                    $stmt->bindParam(':piatDate', $record1['piatDate']);
                    $stmt->bindParam(':projectNo', $record1['projectNo']);
                    $stmt->bindParam(':projectName', $record1['projectName']);
                    $stmt->bindParam(':feederCircuit', $record1['feederCircuit']);
                    $stmt->bindParam(':feederCircuitFrom', $record1['feederCircuitFrom']);
                    $stmt->bindParam(':feederCircuitTo', $record1['feederCircuitTo']);
                    $stmt->bindParam(':feederCircuitLength', $record1['feederCircuitLength']);
                    $stmt->bindParam(':voltageLevel', $record1['voltageLevel']);
                    $stmt->bindParam(':cableType', $record1['cableType']);
                    $stmt->bindParam(':company', $record1['companyJson']);
                    $stmt->bindParam(':companyName', $record1['companyNameJson']);
                    $stmt->bindParam(':companyPhoneNo', $record1['companyPhoneNoJson']);
                    $stmt->bindParam(':companySign', $record1['companySignJson']);
                    $stmt->bindParam(':inspectionChecklist', $record1['checklistArryJson']);
                    $stmt->bindParam(':result', $record1['result']);
                    $stmt->bindParam(':attemptNo', $record1['attemptNo']);
                    $stmt->bindParam(':prepareBy', $record1['prepareBy']);
                    $stmt->bindParam(':prepSign',$record1['prepSign']);


                    $stmt->execute();

                    // remove check list recoered from inspection_checklist
                    $stmt = $pdo->prepare("DELETE  FROM public.inspection_checklist WHERE project_no = :sn ");

            $stmt->bindParam(':sn', $sn);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
        }
                

            // remove qr recored from   ad_service_qr
            $stmt = $pdo->prepare("DELETE FROM public.ad_service_qr WHERE no_sn = :sn ");

            $stmt->bindParam(':sn', $sn);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // $stmt = $pdo->prepare("DELETE  FROM public.sn_monitoring WHERE sn_number = :sn ");

        // $stmt->bindParam(':sn', $sn);
        // $stmt->execute();
        // $record = $stmt->fetch(PDO::FETCH_ASSOC);

        // $stmt = $pdo->prepare("DELETE FROM public.inspection_checklist WHERE project_no = :sn ");

        // $stmt->bindParam(':sn', $sn);
        // $stmt->execute();
        // $record = $stmt->fetch(PDO::FETCH_ASSOC);
 
        $pdo = null;
        
        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'Remove successfully'; 
    
        
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;

        $_SESSION['message'] = 'request failed'; 
        $_SESSION['alert'] = 'alert-danger';
    
    }
   

    header("location:../index.php");
}



?>