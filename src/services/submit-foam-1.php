<?php
session_start();
ob_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $jenis_sn = $_POST['jenis_sn'];
        $jenis_sambungan = $_POST['jenis_sambungan'];
        $no_sn = $_POST['no_sn'];
        $alamat = $_POST['alamat'];
        $tarikh_siap = $_POST['tarikh_siap'];
        $piat = $_POST['piat'];
        $nama_feeder_pillar = $_POST['nama_feeder_pillar'];
        $nama_jalan = $_POST['nama_jalan'];
        $jumlah_span = $_POST['jumlah_span'];
        $talian_utama = isset($_POST['talian_utama']) ? $_POST['talian_utama'] : '';
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
        $stat = "Inprocess";

      

        if ($jenis_sambungan == 'OH') {
            $piat = 'yes';
        } else {
            $piat = 'no';
        }

        if (!isset($_POST['id'])) {
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

            $stmt->bindParam(':status', $status);
        } else {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE public.ad_service_qr SET
                no_sn = :no_sn,
               
                jenis_sn = :jenis_sn,
                jenis_sambungan = :jenis_sambungan,
                alamat = :alamat,
                tarikh_siap = :tarikh_siap,
                piat = :piat,
                nama_feeder_pillar = :nama_feeder_pillar,
                nama_jalan = :nama_jalan,
                jumlah_span = :jumlah_span,
                talian_utama = :talian_utama,
                bil_umbang = :bil_umbang,
                bil_black_box = :bil_black_box,
                bil_lvpt = :bil_lvpt,
                bilangan_serbis = :bilangan_serbis,
                catatan = :catatan,
                seksyen_dari = :seksyen_dari,
                seksyen_ke = :seksyen_ke,
                bil_saiz_tiang_a = :bil_saiz_tiang_a,
                bil_saiz_tiang_b = :bil_saiz_tiang_b,
                bil_saiz_tiang_c = :bil_saiz_tiang_c,
                bil_jenis_spun = :bil_jenis_spun,
                bil_jenis_konkrit = :bil_jenis_konkrit,
                bil_jenis_besi = :bil_jenis_besi,
                bil_jenis_kayu = :bil_jenis_kayu,
                abc_span_a = :abc_span_a,
                abc_span_b = :abc_span_b,
                abc_span_c = :abc_span_c,
                abc_span_d = :abc_span_d,
                pvc_span_a = :pvc_span_a,
                pvc_span_b = :pvc_span_b,
                pvc_span_c = :pvc_span_c,
                bare_span_a = :bare_span_a,
                bare_span_b = :bare_span_b,
                bare_span_c = :bare_span_c,
                qr = 'true'
                WHERE id = :id");

            $stmt->bindParam(':id', $id);
        }

        // $stmt->bindParam(':status', $status);

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
        echo 'DSfdsfsdf';
        if ($jenis_sambungan == 'OH' && $_POST['submit_button'] == 'next') {
            $_SESSION['sn'] = $no_sn;

            header('location:./foamRedirect.php');
            exit();
        }

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
?>
