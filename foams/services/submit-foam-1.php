<?php
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start(); 
    try {
  // Retrieve form data
  $ba = $_POST['ba'];
  $jenis_sn = $_POST['jenis_sn'];
  $jenis_sambungan = $_POST['jenis_sambungan'];
  $no_sn = $_POST['no_sn'];
  $alamat = $_POST['alamat'];
  $tarikh_siap = $_POST['tarikh_siap'];
  $piat = $_POST['piat'];
  $nama_feeder_pillar = $_POST['nama_feeder_pillar'];
  $nama_jalan = $_POST['nama_jalan'];
  $jumlah_span = $_POST['jumlah_span'];
  $talian_utama = $_POST['talian_utama'];
  $bil_umbang = $_POST['bil_umbang'];
  $bil_black_box = $_POST['bil_black_box'];
  $bil_lvpt = $_POST['bil_lvpt'];
  $bilangan_serbis = $_POST['bilangan_serbis'];
  $catatan = $_POST['catatan'];
  $seksyen_dari = $_POST['seksyen_dari'];
  $seksyen_ke = $_POST['seksyen_ke'];
  $bil_saiz_tiang_a = $_POST['bil_saiz_tiang_a'];
  $bil_saiz_tiang_b = $_POST['bil_saiz_tiang_b'];
  $bil_saiz_tiang_c = $_POST['bil_saiz_tiang_c'];
  $bil_jenis_spun = $_POST['bil_jenis_spun'];
  $bil_jenis_konkrit = $_POST['bil_jenis_konkrit'];
  $bil_jenis_besi = $_POST['bil_jenis_besi'];
  $bil_jenis_kayu = $_POST['bil_jenis_kayu'];
  $abc_span_a = $_POST['abc_span_a'];
  $abc_span_b = $_POST['abc_span_b'];
  $abc_span_c = $_POST['abc_span_c'];
  $abc_span_d = $_POST['abc_span_d'];
  $pvc_span_a = $_POST['pvc_span_a'];
  $pvc_span_b = $_POST['pvc_span_b'];
  $pvc_span_c = $_POST['pvc_span_c'];
  $bare_span_a = $_POST['bare_span_a'];
  $bare_span_b = $_POST['bare_span_b'];
  $bare_span_c = $_POST['bare_span_c'];
 


    $stmt = $pdo->prepare("SELECT count(*) FROM public.ad_service_qr WHERE no_sn = :sn");
    $stmt->bindParam(':sn', $no_sn);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
 


    if ($record && $record['count'] > 0) {
        $pdo = null;
        $_SESSION['message'] = 'SN no already exists'; 
        $_SESSION['alert'] = 'alert-danger';
        header("Location: ../index.php");
        exit();
    }
 
    $stmt = $pdo->prepare("INSERT INTO public.ad_service_qr (
        ba, jenis_sn, jenis_sambungan, no_sn, alamat, tarikh_siap, piat, nama_feeder_pillar, nama_jalan,
        jumlah_span, talian_utama, bil_umbang, bil_black_box, bil_lvpt, bilangan_serbis, catatan,
        seksyen_dari, seksyen_ke, bil_saiz_tiang_a, bil_saiz_tiang_b, bil_saiz_tiang_c,
        bil_jenis_spun, bil_jenis_konkrit, bil_jenis_besi, bil_jenis_kayu, abc_span_a, abc_span_b,
        abc_span_c, abc_span_d, pvc_span_a, pvc_span_b, pvc_span_c, bare_span_a, bare_span_b, bare_span_c
    ) VALUES (:ba, :jenis_sn, :jenis_sambungan, :no_sn, :alamat, :tarikh_siap, :piat, :nama_feeder_pillar,
        :nama_jalan, :jumlah_span, :talian_utama, :bil_umbang, :bil_black_box, :bil_lvpt,
        :bilangan_serbis, :catatan, :seksyen_dari, :seksyen_ke, :bil_saiz_tiang_a, :bil_saiz_tiang_b,
        :bil_saiz_tiang_c, :bil_jenis_spun, :bil_jenis_konkrit, :bil_jenis_besi, :bil_jenis_kayu,
        :abc_span_a, :abc_span_b, :abc_span_c, :abc_span_d, :pvc_span_a, :pvc_span_b, :pvc_span_c,
        :bare_span_a, :bare_span_b, :bare_span_c)");

    $stmt->bindParam(':ba', $ba);
    $stmt->bindParam(':jenis_sn', $jenis_sn);
    $stmt->bindParam(':jenis_sambungan', $jenis_sambungan);
    $stmt->bindParam(':no_sn', $no_sn);
    $stmt->bindParam(':alamat', $alamat);
    $stmt->bindParam(':tarikh_siap', $tarikh_siap);
    $stmt->bindParam(':piat', $piat);
    $stmt->bindParam(':nama_feeder_pillar', $nama_feeder_pillar);
    $stmt->bindParam(':nama_jalan', $nama_jalan);
    $stmt->bindParam(':jumlah_span', $jumlah_span);
    $stmt->bindParam(':talian_utama', $talian_utama);
    $stmt->bindParam(':bil_umbang', $bil_umbang);
    $stmt->bindParam(':bil_black_box', $bil_black_box);
    $stmt->bindParam(':bil_lvpt', $bil_lvpt);
    $stmt->bindParam(':bilangan_serbis', $bilangan_serbis);
    $stmt->bindParam(':catatan', $catatan);
    $stmt->bindParam(':seksyen_dari', $seksyen_dari);
    $stmt->bindParam(':seksyen_ke', $seksyen_ke);
    $stmt->bindParam(':bil_saiz_tiang_a', $bil_saiz_tiang_a);
    $stmt->bindParam(':bil_saiz_tiang_b', $bil_saiz_tiang_b);
    $stmt->bindParam(':bil_saiz_tiang_c', $bil_saiz_tiang_c);
    $stmt->bindParam(':bil_jenis_spun', $bil_jenis_spun);
    $stmt->bindParam(':bil_jenis_konkrit', $bil_jenis_konkrit);
    $stmt->bindParam(':bil_jenis_besi', $bil_jenis_besi);
    $stmt->bindParam(':bil_jenis_kayu', $bil_jenis_kayu);
    $stmt->bindParam(':abc_span_a', $abc_span_a);
    $stmt->bindParam(':abc_span_b', $abc_span_b);
    $stmt->bindParam(':abc_span_c', $abc_span_c);
    $stmt->bindParam(':abc_span_d', $abc_span_d);
    $stmt->bindParam(':pvc_span_a', $pvc_span_a);
    $stmt->bindParam(':pvc_span_b', $pvc_span_b);
    $stmt->bindParam(':pvc_span_c', $pvc_span_c);
    $stmt->bindParam(':bare_span_a', $bare_span_a);
    $stmt->bindParam(':bare_span_b', $bare_span_b);
    $stmt->bindParam(':bare_span_c', $bare_span_c);

    $stmt->execute();

    if($jenis_sambungan == "OH/Combine Service"){
        $bal = "";
        if($ba == "KL Barat"){
            $bal = "KLB";
        }elseif ($ba == "KL Timur") {
            $bal = "KLT";
        } elseif ($ba == "KL Pusat") {
            $bal = "KLP";
        }
        elseif ($ba == "KL Selatan") {
            $bal = "KLS";
        }


        $stmt = $pdo->prepare("SELECT * FROM users WHERE station = :bal");
        $stmt->bindParam(':bal', $bal);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        $span_sum = 0;
        $cable_type = '';

        if ($_POST['abc_span_a'] != "") {
            $span_sum += intval($_POST['abc_span_a']);
            $cable_type .= 'ABC-3X185-'.$_POST['abc_span_a'];
        }
        if ($_POST['abc_span_b'] != "") {
            $span_sum += intval($_POST['abc_span_b']);
            $cable_type .= ', ABC-3X95-'.$_POST['abc_span_b'];
        }
        if ($_POST['abc_span_c'] != "") {
            $span_sum += intval($_POST['abc_span_c']);
            $cable_type .= ', ABC-3X16-'.$_POST['abc_span_c'];
        }
        if ($_POST['abc_span_d'] != "") {
            $span_sum += intval($_POST['abc_span_d']);
            $cable_type .= ', ABC-1X16-'.$_POST['abc_span_d'];
        }

        if ($_POST['pvc_span_a'] != "") {
            $span_sum += intval($_POST['pvc_span_a']);
            $cable_type .= ', PVC-19/064-'.$_POST['pvc_span_a'];
        }
        if ($_POST['pvc_span_b'] != "") {
            $span_sum += intval($_POST['pvc_span_b']);
            $cable_type .= ', PVC-7/083-'.$_POST['pvc_span_b'];
        }
        if ($_POST['pvc_span_c'] != "") {
            $span_sum += intval($_POST['pvc_span_c']);
            $cable_type .= ', PVC-7/044-'.$_POST['pvc_span_c'];
        }

        if ($_POST['bare_span_a'] != "") {
            $span_sum += intval($_POST['bare_span_a']);
            $cable_type .= ', BARE-7/173-'.$_POST['bare_span_a'];
        }
        if ($_POST['bare_span_b'] != "") {
            $span_sum += intval($_POST['bare_span_b']);
            $cable_type .= ', BARE-7/122-'.$_POST['bare_span_b'];
        }
        if ($_POST['bare_span_c'] != "") {
            $span_sum += intval($_POST['bare_span_c']);
            $cable_type .= ', BARE-3/132-'.$_POST['bare_span_c'];
        }

        
        
        $_SESSION['nama_jalan'] = $nama_jalan;
        $_SESSION['cable_type'] = $cable_type;
        $_SESSION['span_sum'] = $span_sum;
        $_SESSION['user_data'] = $rows;
        $_SESSION['no_sn'] = $no_sn;
        $_SESSION['alamat'] = $alamat;
        $_SESSION['tarikh_siap'] = $tarikh_siap;

        header("Location: ../foam.php");
        exit();
    }
    
   
    $_SESSION['alert'] = 'alert-success';
    $_SESSION['message'] = 'inserted successfully'; 

} catch (PDOException $e) {
    // echo  $e->getMessage();
    // exit();
    session_start(); 
    $_SESSION['message'] = 'inserted failed'; 
    $_SESSION['alert'] = 'alert-danger';
    
}
    $pdo = null;
   
    header("Location: ../index.php");
     exit();
}
?>
