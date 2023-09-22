<?php
ob_start();
session_start();
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

include './connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exc_date_type'])) {
    if ($_SESSION['user_name'] == 'admin') {
        $ba = isset($_POST['exc_ba']) ? $_POST['exc_ba'] : '';
    } else {
        $ba = $_SESSION['user_ba'];
    }

    $from = isset($_POST['exc_from']) ? $_POST['exc_from'] : '';
    $to = isset($_POST['exc_to']) ? $_POST['exc_to'] : '';
    $record = '';
    if ($from == '' || $to == '') {
        // if dates are null and only ba is selected then first get min and max date
        $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != ''");
        $stmt->execute();
        $comp_date = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('SELECT MAX(csp_paid_date) AS max_date, MIN(csp_paid_date) AS min_date FROM public.ad_service_qr ');
        $stmt->execute();
        $csp_date = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (isset($_POST['exc_date_type'])) {
        if ($_POST['exc_date_type'] == 'CSP') {
            $col_name = 'csp_paid_date';
            $from = $from == '' ? $comp_date['min_date'] : $from;
            $to = $to == '' ? $comp_date['max_date'] : $to;
        } elseif ($_POST['exc_date_type'] == 'Completion') {
            $col_name = 'tarikh_siap';
            $from = $from == '' ? $csp_date['min_date'] : $from;
            $to = $to == '' ? $csp_date['max_date'] : $to;
        } else {
            $from_siap = $from == '' ? $comp_date['min_date'] : $from;
            $to_siap = $to == '' ? $comp_date['max_date'] : $to;
            $from_paid = $from == '' ? $csp_date['min_date'] : $from;
            $to_paid = $to == '' ? $csp_date['max_date'] : $to;
            $col_name = 'both';
        }
    }

    if ($col_name == 'both') {
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba 
                    AND csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid OR ba LIKE :ba 
                    AND tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap");
        $stmt->bindParam(':from_paid', $from_paid);
        $stmt->bindParam(':to_paid', $to_paid);
        $stmt->bindParam(':from_siap', $from_siap);
        $stmt->bindParam(':to_siap', $to_siap);
        $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare(
            "SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba 
                    AND " .
                $col_name .
                ' >= :from AND ' .
                $col_name .
                ' <= :to',
        );

        $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
    }
} elseif (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr ');
    $stmt->execute();
}

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdo = null;

if ($records) {
    $name = isset($_REQUEST['id']) ? $records[0]['no_sn'] : 'All Sn';

    try {
        if ($_POST['submit-button'] == 'download-sn') {
            $spreadsheet = IOFactory::load('../../assets/sn-excel-template.xlsx');

            $sheet = $spreadsheet->getActiveSheet();
            $i = 2;

            foreach ($records as $record) {
                $sheet->setCellValue('A' . $i, $i - 1);
                $sheet->setCellValue('B' . $i, $record['ba']);
                $sheet->setCellValue('C' . $i, $record['alamat']);
				$sheet->setCellValue('D' . $i, $record['no_sn']);
                $sheet->setCellValue('E' . $i, $record['user_status']);
                $sheet->setCellValue('F' . $i, $record['jenis_sn']);
                $sheet->setCellValue('G' . $i, $record['jenis_sambungan']);
                $sheet->setCellValue('H' . $i, $record['csp_paid_date']);
                $sheet->setCellValue('I' . $i, $record['pic_vendor']);
                $sheet->setCellValue('J' . $i, $record['remark'] == '' ? '-' : $record['remark']);
                $sheet->setCellValue('K' . $i, $record['status'] == '' ? '-' : $record['status']);
                $sheet->setCellValue('L' . $i, $record['tarikh_siap'] == '' ? '-' : $record['tarikh_siap']);
                $sheet->setCellValue('M' . $i, $record['erms_status'] == '' ? '-' : $record['erms_status']);

                $i++;
            }
        } else {
            $spreadsheet = IOFactory::load('../../assets/Excel- template.xlsx');

            $sheet = $spreadsheet->getActiveSheet();
            $i = 8;

            foreach ($records as $record) {
                $sheet->setCellValue('B' . $i, $i - 7);
                $sheet->setCellValue('C' . $i, $record['ba']);
                $sheet->setCellValue('D' . $i, $record['jenis_sambungan']);
                $sheet->setCellValue('E' . $i, $record['jenis_sn']);
                $sheet->setCellValue('F' . $i, $record['no_sn']);
                $sheet->setCellValue('G' . $i, $record['alamat']);
                $sheet->setCellValue('H' . $i, $record['tarikh_siap']);
                $sheet->setCellValue('I' . $i, $record['piat']);
                $sheet->setCellValue('J' . $i, $record['nama_feeder_pillar']);
                $sheet->setCellValue('K' . $i, $record['nama_jalan']);
                $sheet->setCellValue('N' . $i, $record['seksyen_dari']);
                $sheet->setCellValue('O' . $i, $record['seksyen_ke']);
                $sheet->setCellValue('P' . $i, $record['bil_saiz_tiang_a']);
                $sheet->setCellValue('Q' . $i, $record['bil_saiz_tiang_b']);
                $sheet->setCellValue('R' . $i, $record['bil_saiz_tiang_c']);
                $sheet->setCellValue('S' . $i, $record['bil_jenis_spun']);
                $sheet->setCellValue('T' . $i, $record['bil_jenis_konkrit']);
                $sheet->setCellValue('U' . $i, $record['bil_jenis_besi']);
                $sheet->setCellValue('V' . $i, $record['bil_jenis_kayu']);
                $sheet->setCellValue('X' . $i, $record['abc_span_a']);
                $sheet->setCellValue('Y' . $i, $record['abc_span_b']);
                $sheet->setCellValue('Z' . $i, $record['abc_span_c']);
                $sheet->setCellValue('AA' . $i, $record['abc_span_d']);
                $sheet->setCellValue('AB' . $i, $record['pvc_span_a']);
                $sheet->setCellValue('AC' . $i, $record['pvc_span_b']);
                $sheet->setCellValue('AD' . $i, $record['pvc_span_c']);
                $sheet->setCellValue('AE' . $i, $record['bare_span_a']);
                $sheet->setCellValue('AF' . $i, $record['bare_span_b']);
                $sheet->setCellValue('AG' . $i, $record['bare_span_c']);
                $sheet->setCellValue('AH' . $i, $record['jumlah_span']);
                $sheet->setCellValue('AI' . $i, $record['talian_utama']);
                $sheet->setCellValue('AJ' . $i, $record['bil_umbang']);
                $sheet->setCellValue('AK' . $i, $record['bil_black_box']);
                $sheet->setCellValue('AL' . $i, $record['bil_lvpt']);
                $sheet->setCellValue('AN' . $i, $record['bilangan_serbis']);
                $sheet->setCellValue('AO' . $i, $record['catatan']);
                $i++;
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('../../assets/Excel/' . $name . '.xlsx');

        ob_end_clean();

        // Send headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . filesize('../../assets/Excel/' . $name . '.xlsx'));

        // Output the file
        readfile('../../assets/Excel/' . $name . '.xlsx');
        exit();
    } catch (Exception $e) {
        header('location : ../index.php');
        exit();
        echo 'Error: ' . $e->getMessage();
    }
}else{
    $_SESSION['message'] = 'no records found';
    $_SESSION['alert'] = 'alert-danger';
    header('location:./index.php');
}

?>
