<?php
session_start();
ob_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $sn = $_REQUEST['sn'];

 
    try{

        $stmt = $pdo->prepare("SELECT no_sn  FROM public.ad_service_qr WHERE no_sn = :sn ");

        $stmt->bindParam(':sn', $sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        

        if ($record) {
            $stmt = $pdo->prepare("INSERT INTO public.ad_service_qr (
                jenis_sn, jenis_sambungan, no_sn, alamat, tarikh_siap, piat, nama_feeder_pillar, nama_jalan,
               jumlah_span, talian_utama, bil_umbang, bil_black_box, bil_lvpt, bilangan_serbis, catatan,
               seksyen_dari, seksyen_ke, bil_saiz_tiang_a, bil_saiz_tiang_b, bil_saiz_tiang_c,
               bil_jenis_spun, bil_jenis_konkrit, bil_jenis_besi, bil_jenis_kayu, abc_span_a, abc_span_b,
               abc_span_c, abc_span_d, pvc_span_a, pvc_span_b, pvc_span_c, bare_span_a, bare_span_b, bare_span_c, status
           ) VALUES ( :jenis_sn, :jenis_sambungan, :no_sn, :alamat, :tarikh_siap, :piat, :nama_feeder_pillar,
               :nama_jalan, :jumlah_span, :talian_utama, :bil_umbang, :bil_black_box, :bil_lvpt,
               :bilangan_serbis, :catatan, :seksyen_dari, :seksyen_ke, :bil_saiz_tiang_a, :bil_saiz_tiang_b,
               :bil_saiz_tiang_c, :bil_jenis_spun, :bil_jenis_konkrit, :bil_jenis_besi, :bil_jenis_kayu,
               :abc_span_a, :abc_span_b, :abc_span_c, :abc_span_d, :pvc_span_a, :pvc_span_b, :pvc_span_c,
               :bare_span_a, :bare_span_b, :bare_span_c, :status)");
               
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
    

            $stmt->bindParam(':sn', $sn);
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE  FROM public.ad_service_qr WHERE no_sn = :sn ");

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


        // $_SESSION['message'] = ' failed'; 
        // $_SESSION['alert'] = 'alert-danger';
    
    }
   

    header("location:../index.php");
}



?>